<?php

namespace App\Http\Controllers\admin\third_party;

use App\Http\Controllers\Controller;
use App\Mail\SendEmailToThirdPartyQuestionnaireContact;
use App\Models\Asset;
use App\Models\AssetGroup;
use App\Models\FrameworkControl;
use App\Models\Impact;
use App\Models\Likelihood;
use App\Models\ScoringMethod;
use App\Models\Tag;
use App\Models\ThirdPartyContactQuestionnaire;
use App\Models\ThirdPartyContactQuestionnaireAnswer;
use App\Models\ThirdPartyContactQuestionnaireAnswerResult;
use App\Models\ThirdPartyProfileContact;
use App\Models\ThirdPartyQuestionnaire;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use Yajra\DataTables\Facades\DataTables;

class ThirdPartyQuestionnaireResultController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $thirdPartyQuestionnaireAnswer = ThirdPartyContactQuestionnaireAnswer::join(
                'third_party_profile_contact as contact',
                'contact.id',
                '=',
                'third_party_contact_questionnaire_answer.contact_id'
            )
                ->join(
                    'third_party_profiles as profile',
                    'profile.id',
                    '=',
                    'contact.third_party_profile_id'
                )
                ->join(
                    'third_party_questionnaires as questionnaire',
                    'questionnaire.id',
                    '=',
                    'third_party_contact_questionnaire_answer.questionnaire_id'
                )
                ->select(
                    'third_party_contact_questionnaire_answer.*',
                    'questionnaire.name as questionnaire_name',
                    'contact.email as contact_email',
                    'contact.name as contact_name',
                    'profile.third_party_name as third_party_name'
                )
                ->orderBy('created_at', 'desc')
                ->get();

            // dd($thirdPartyQuestionnaireAnswer);

            return DataTables::of($thirdPartyQuestionnaireAnswer)
                ->addColumn('actions', function ($thirdPartyQuestionnaireAnswer) {
                    // Initialize an empty string to hold the dropdown menu items
                    $dropdownItems = '';

                    if ($thirdPartyQuestionnaireAnswer->submission_type != "draft") {
                        // View button
                        $dropdownItems .= '<a href="' . route("admin.third_party.viewQuestionnaireAnswer", encrypt($thirdPartyQuestionnaireAnswer->id)) . '" class="dropdown-item view-questionnaire_result"
                                                data-id="' . $thirdPartyQuestionnaireAnswer->id . '">
                                                <i class="fas fa-eye me-2"></i>' . __("locale.View") . '
                                            </a>';
                        // $dropdownItems .= '<a href="javascript:void(0)" class="dropdown-item  view-questionnaire_result"
                        //                         data-id="' . $thirdPartyQuestionnaireAnswer->id . '">
                        //                         <i class="fas fa-eye me-2"></i>View
                        //                     </a>';
                    }

                    // Return the HTML content for the dropdown menu with `dropup` class
                    return '<div class="d-inline-flex dropup">
                                <a class="pe-1 dropdown-toggle hide-arrow text-primary" data-bs-toggle="dropdown" aria-expanded="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="feather feather-more-vertical font-small-4">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="12" cy="5" r="1"></circle>
                                        <circle cx="12" cy="19" r="1"></circle>
                                    </svg>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded" aria-labelledby="dropdownMenuButton">
                                    ' . $dropdownItems . '
                                </div>
                            </div>';
                })
                // ->editColumn('created_at', function ($model) {
                //     return Carbon::parse($model->created_at)->format('d/m/Y h:i A'); // 12-hour format with AM/PM
                // })
                ->editColumn('send_date', function ($model) {
                    return Carbon::parse($model->send_date)->format('d/m/Y h:i A'); // 12-hour format with AM/PM
                })
                ->editColumn('submission_date', function ($model) {
                    return $model->submission_date
                        ? Carbon::parse($model->submission_date)->format('d/m/Y h:i A')
                        : __('third_party.Not submitted yet');
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        // dd("requests");

        $breadcrumbs = [
            ['link' => route('admin.dashboard'), 'name' => __('locale.Dashboard')],
            ['name' => __('locale.ThirdPartyManagment')],
            ['link' => route('admin.third_party.questionnaires'), 'name' => __('locale.Questionnaires')],
            ['name' => __('locale.Results')]
        ];


        $data = [
            // 'breadcrumbs' => $breadcrumbs,
        ];

        return view('admin.content.third_party.assessments.results.index', compact('breadcrumbs', 'data'));
    }

    public function view($questionnaireAnswerId)
    {
        try {
            $requestReccipientId = DB::table('third_party_request_recipients')->value('user_id');
            $decryptQuestionnaireAnswerId = decrypt($questionnaireAnswerId);

            $answerData = ThirdPartyContactQuestionnaireAnswer::join(
                'third_party_profile_contact as contact',
                'contact.id',
                '=',
                'third_party_contact_questionnaire_answer.contact_id'
            )
                ->join(
                    'third_party_questionnaires as questionnaire',
                    'questionnaire.id',
                    '=',
                    'third_party_contact_questionnaire_answer.questionnaire_id'
                )
                ->select(
                    'third_party_contact_questionnaire_answer.*',
                    'questionnaire.name as questionnaire_name',
                    'contact.name as contact_name',
                    'contact.email as contact_email',
                )
                ->findOrFail($decryptQuestionnaireAnswerId);

            $answerResults = ThirdPartyContactQuestionnaireAnswerResult::where('third_party_contact_questionnaire_answer_result.contact_questionnaire_answer_id', $decryptQuestionnaireAnswerId)
                ->join('questions', 'questions.id', '=', 'third_party_contact_questionnaire_answer_result.question_id')
                ->leftJoin('assessment_answers as answers', 'answers.id', '=', 'third_party_contact_questionnaire_answer_result.answer_id')
                ->select(
                    'third_party_contact_questionnaire_answer_result.id as id',
                    'questions.question as question',
                    'answers.answer as answer',
                    'third_party_contact_questionnaire_answer_result.comment as comment',
                )
                ->get();

            $questionnaireRisks = ThirdPartyQuestionnaire::where('third_party_questionnaires.id', $answerData->questionnaire_id)
                ->join('third_party_questionnaire_risk as risks', 'risks.questionnaire_id', '=', 'third_party_questionnaires.id')
                ->select(
                    'risks.*'
                )
                ->get();

            $likelihoods = Likelihood::query()->select(['id', 'name'])->get();
            $impacts = Impact::query()->select(['id', 'name'])->get();
            $enabledUsers = User::query()->where('enabled', true)->get();
            $assetGroups = AssetGroup::all();
            $assets = Asset::select('id', 'name')->orderBy('id')->get();
            $tags = Tag::all();
            $migration_controls = FrameworkControl::query()->get(['id', 'short_name as name']);
            $riskScoringMethods = ScoringMethod::all();

            $data = [
                'answerData' => $answerData,
                'results' => $answerResults,
                'questionnaireRisks' => $questionnaireRisks,
                'likelihoods' => $likelihoods,
                'impacts' => $impacts,
                'enabledUsers' => $enabledUsers,
                'assetGroups' => $assetGroups,
                'assets' => $assets,
                'tags' => $tags,
                'migration_controls' => $migration_controls,
                'riskScoringMethods' => $riskScoringMethods,
                'requestReccipientId' => $requestReccipientId
            ];

            $breadcrumbs = [
                ['link' => route('admin.dashboard'), 'name' => __('locale.Dashboard')],
                ['link' => route('admin.third_party.reports'), 'name' => __('locale.ThirdPartyManagment')],
                ['link' => route('admin.third_party.questionnaires'), 'name' => __('locale.Questionnaires')],
                ['link' => route('admin.third_party.questionnairesResults'), 'name' => __('locale.Results')]
            ];

            // dd($data);
            return view('admin.content.third_party.assessments.results.view', compact('data', 'breadcrumbs'));
        } catch (\Exception $exception) {
            abort(403);
        }
    }

    public function updateStatus(Request $request, $questionnaireAnswerId)
    {
        $answerData = ThirdPartyContactQuestionnaireAnswer::findOrFail($questionnaireAnswerId);
        $questionnaireId = $answerData->questionnaire_id;

        if ($request->approved_status == 'remeidation') {
            try {
                DB::beginTransaction();

                $answerData->update([
                    'submission_type' => 'draft',
                    'status' => 'incomplete',
                    'percentage_complete' => 0,
                    'send_date' => now(),
                    'submission_date' => null
                ]);

                $this->sendRemedationEmail($questionnaireId, $questionnaireAnswerId);

                $message = 'Remediation email sent successfully';

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'message' => 'Sent email went error',
                    'errors' => $th->errors(),
                ], 422);
            }
        } else if ($request->approved_status == 'yes') {
            $message = 'Questionnaire approved successfully';
        } else {
            $message = 'Questionnaire rejected successfully';
        }

        $answerData->update([
            'approved_status' => $request->approved_status,
            'note' => $request->note ?? null,
        ]);

        return response()->json([
            'message' => $message,
        ], 200);
    }

    public function sendRemedationEmail($questionnaireId, $questionnaireAnswerId)
    {

        $questionnaire = ThirdPartyQuestionnaire::findOrFail($questionnaireId);
        $questionnaireContacts = ThirdPartyContactQuestionnaire::where('questionnaire_id', $questionnaireId)->get();
        $answerData = ThirdPartyContactQuestionnaireAnswer::findOrFail($questionnaireAnswerId);

        // dd($questionnaireContacts);

        foreach ($questionnaireContacts->toArray() as $contact) {
            $questionnaireContactEmail = ThirdPartyProfileContact::where('id', $contact['contact_id'])->value('email');
            $questionnaireContactName = ThirdPartyProfileContact::where('id', $contact['contact_id'])->value('name');
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_$@/-=.!~\?+';
            $randomString = substr(str_shuffle(str_repeat($characters, 5)), 0, 15);

            $questionnaire_contacts[] = [
                'id' => $contact['contact_id'],
                'name' => $questionnaireContactName,
                'email' => $questionnaireContactEmail,
                'access_password' => $randomString,
            ];
            // dd($questionnaire_contacts);

            $answerData->update([
                'access_password' => $randomString,
                'send_date' => now(),
            ]);
        }

        foreach ($questionnaire_contacts as $questionnaire_contact) {
            $contactEmail = $questionnaire_contact['email'];
            $body = new SendEmailToThirdPartyQuestionnaireContact($questionnaire, $questionnaire_contact, $answerData->note);
            $bodyContent = $body->render();

            $this->pushEmail($contactEmail, $bodyContent);
        }
    }

    public function pushEmail($contactEmail, $bodyContent)
    {
        $email_to = $contactEmail;
        $email_config = DB::table('email_config')->first();

        if (!$email_config) {
            // Handle the case where email configuration is not found
            $response = [
                'status' => false,
                'message' => __('error_occured'),
            ];
            return response()->json($response, 500);
        }

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        try {
            $mail->isSMTP();
            $mail->isHTML(true);
            $mail->SMTPDebug = false;
            $mail->Mailer = "smtp";
            $mail->SMTPAuth = false;
            $mail->Port = $email_config->smtp_port;
            $mail->Host = $email_config->smtp_server;
            $mail->Username = $email_config->smtp_username;
            $mail->Password = base64_decode($email_config->smtp_password);
            $mail->SMTPSecure = $email_config->ssl_tls;
            $mail->isHTML(true);
            $mail->addAddress($email_to);
            $mail->setFrom($email_config->smtp_username, $email_config->smtp_from_username);

            $mail->Body = $bodyContent;

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false
                )
            );

            if ($mail->send()) {
                $response = [
                    'status' => true,
                    'message' => __('success'),
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => __('error_occured'),
                ];
            }
        } catch (Exception $e) {
            $response = [
                'status' => false,
                'message' => __('error_occured'),
            ];
        }

        return response()->json($response, 500);
    }
}
