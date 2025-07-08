<?php

namespace App\Http\Controllers\admin\third_party;

use App\Http\Controllers\Controller;
use App\Mail\SendEmailToThirdPartyQuestionnaireContact;
use App\Models\{
    Assessment,
    AssessmentAnswer,
    Question,
    ThirdPartyContactQuestionnaire,
    ThirdPartyContactQuestionnaireAnswerResult,
    ThirdPartyProfileContact,
    ThirdPartyQuestionnaire,
    ThirdPartyQuestionnaireQuestion,
    ThirdPartyRequest,
    ThirdPartyContactQuestionnaireAnswer,
    ThirdPartyQuestionnaireRisk,
};
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ThirdPartyQuestionnaireController extends Controller
{

    public function index(Request $request)
    {
        if (auth()->user()->hasPermission('third_party_assessment.list')) {

            if ($request->ajax()) {
                $requestReccipientId = DB::table('third_party_request_recipients')->value('user_id');
                $questionnierIdsFromAnswers = ThirdPartyContactQuestionnaireAnswer::query()->pluck('questionnaire_id')->toArray();

                $questionnierIdsFromAnswersWithAction = ThirdPartyContactQuestionnaireAnswer::whereNotNull('approved_status')
                    ->pluck('questionnaire_id')->toArray();

                $thirdPartyQuestionnaire = ThirdPartyQuestionnaire::with(['assessment', 'request'])
                    ->select(
                        'id',
                        'name',
                        'instructions',
                        'request_id',
                        'assessment_id',
                        'created_at'
                    )
                    ->orderBy('created_at', 'desc');

                return DataTables::of($thirdPartyQuestionnaire)
                    ->addColumn('assessment', function ($thirdPartyQuestionnaire) {
                        return $thirdPartyQuestionnaire->assessment->name; // Assuming you have a assessment relation
                    })
                    ->addColumn('request', function ($thirdPartyQuestionnaire) {
                        return $thirdPartyQuestionnaire->request->profile->third_party_name; // Assuming you have a request relation
                    })
                    ->addColumn('actions', function ($thirdPartyQuestionnaire) use ($questionnierIdsFromAnswersWithAction, $questionnierIdsFromAnswers, $requestReccipientId) {

                        // Initialize an empty string to hold the dropdown menu items
                        $dropdownItems = '';

                        // View button
                        $dropdownItems .= '<a href="javascript:void(0)" class="dropdown-item  view-questionnaire"
                                            data-id="' . $thirdPartyQuestionnaire->id . '">
                                            <i class="fas fa-eye me-2"></i>' . __('locale.View') . '
                                        </a>';

                        if (
                            !in_array($thirdPartyQuestionnaire->id, $questionnierIdsFromAnswers) &&
                            (auth()->user()->hasPermission('third_party_assessment.update') || auth()->user()->id == $requestReccipientId)
                        ) {
                            // Edit button
                            $dropdownItems .= '<a href="javascript:void(0)" class="dropdown-item  edit-questionnaire"
                                                data-id="' . $thirdPartyQuestionnaire->id . '">
                                                <i class="fas fa-edit me-2"></i>' . __('locale.Edit') . '
                                            </a>';
                        }

                        if (
                            !in_array($thirdPartyQuestionnaire->id, $questionnierIdsFromAnswers) &&
                            (auth()->user()->hasPermission('third_party_assessment.delete') || auth()->user()->id == $requestReccipientId)
                        ) {
                            // Delete button
                            $dropdownItems .= '<a href="javascript:void(0)" class="dropdown-item  delete-questionnaire"
                                                data-id="' . $thirdPartyQuestionnaire->id . '">
                                                <i class="fas fa-trash me-2"></i>' . __('locale.Delete') . '
                                            </a>';
                        }

                        if (!in_array($thirdPartyQuestionnaire->id, $questionnierIdsFromAnswersWithAction) && auth()->user()->id == $requestReccipientId) {
                            // Send questionnaire mail button
                            $dropdownItems .= '<a href="javascript:void(0)" class="dropdown-item send-questionnaire"
                                                data-id="' . $thirdPartyQuestionnaire->id . '">
                                                <i class="fa fa-paper-plane fa-sm me-2"></i>' . __('locale.Send') . '
                                            </a>';
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
                    ->editColumn('created_at', function ($model) {
                        return Carbon::parse($model->created_at)->format('d/m/Y h:i A'); // 12-hour format with AM/PM
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            // dd("requests");
            $breadcrumbs = [
                ['link' => route('admin.dashboard'), 'name' => __('locale.Dashboard')],
                ['name' => __('locale.ThirdPartyManagment')],
                ['name' => __('locale.Questionnaires')]
            ];


            $data = [
                // 'breadcrumbs' => $breadcrumbs,
            ];

            return view('admin.content.third_party.assessments.index', compact('breadcrumbs', 'data'));
        } else {
            abort(403);
        }
    }


    // this function return view create or update form
    public function getForm(Request $request, $type, $id)
    {
        // dd($request_id);
        if ($request->ajax()) {

            if ($type == 'create_assessment') {
                $request_id = $id;
                $assessments = Assessment::query()->with('questions:id,question')->select('name', 'id')->latest('id')->get();

                // dd($assessments->toArray());
                $thirdPartyRequest = ThirdPartyRequest::findOrFail($request_id);
                $thirdPartyContacts = DB::table('third_party_profile_contact')
                    ->where('third_party_profile_id', $thirdPartyRequest->third_party_profile_id)
                    ->pluck('email', 'id');

                $data = [
                    'request' => $thirdPartyRequest,
                    'assessments' => $assessments,
                    'contacts' => $thirdPartyContacts
                ];

                // dd($data);
                return view('admin.content.third_party.assessments.create', compact('data'));
            } elseif ($type == 'edit_assessment') {
                $questionnaire_id = $id;

                $questionnaire = ThirdPartyQuestionnaire::findOrFail($questionnaire_id);
                $assessments = Assessment::query()->with('questions:id,question')->select('name', 'id')->latest('id')->get();
                $thirdPartyContacts = DB::table('third_party_profile_contact')
                    ->where('third_party_profile_id', $questionnaire->request->third_party_profile_id)
                    ->pluck('email', 'id');
                $questionnaireContacts = ThirdPartyContactQuestionnaire::where('questionnaire_id', $questionnaire_id)->value('contact_id');

                $data = [
                    'questionnaire' => $questionnaire,
                    'assessments' => $assessments,
                    'contacts' => $thirdPartyContacts,
                    'questionnaireContacts' => $questionnaireContacts,
                ];
                // dd($data);
                return view('admin.content.third_party.assessments.edit', compact('data'));
            } else {
                return response()->json(['message' => 'Error: unkown type function'], 404);
            }
        } else {
            abort('403');
        }
    }

    public function createQuestionnaire($request_id, Request $request)
    {
        try {
            // dd($request->all());
            $request->validate([
                'name' => ['required', 'string'],
                'instructions' => ['max:400', 'string'],
                'assessment_id' => ['required', 'exists:assessments,id'],
                'contacts' => ['required'],
            ], [
                // Custom error messages
                'name.required' => __('third_party.assessmentName') . " " . __('third_party.required validation'),
                'name.string' => __('third_party.assessmentName') . " " . __('third_party.string validation'),

                'instructions.string' => __('third_party.Instructions') . " " . __('third_party.string validation'),
                'instructions.max' => __('third_party.Instructions') . " " . __('third_party.max validation') . " 400 " . __("third_party.character"),

                'assessment_id.required' => __('assessment.Assessment') . " " . __('third_party.required validation'),

                'contacts.required' => __('third_party.Contacts') . " " . __('third_party.required validation'),
            ]);

            DB::beginTransaction();

            ThirdPartyRequest::where('id', $request_id)->update([
                'status' => 2 // inassessment
            ]);

            $questionnaireGetId = ThirdPartyQuestionnaire::insertGetId([
                'name' => $request->name,
                'instructions' => $request->instructions,
                'request_id' => $request_id,
                'assessment_id' => $request->assessment_id,
                'all_questions_mandatory' => $request->all_questions_mandatory,
            ]);

            if (!empty($request->contacts)) {
                // Ensure it's always treated as an array for consistent handling.
                $contacts = is_array($request->contacts) ? $request->contacts : [$request->contacts];

                foreach ($contacts as $contact) {
                    ThirdPartyContactQuestionnaire::create([
                        'contact_id' => $contact,
                        'questionnaire_id' => $questionnaireGetId,
                    ]);
                }
            }

            foreach ($request->questions as $question) {
                ThirdPartyQuestionnaireQuestion::create([
                    'questionnaire_id' => $questionnaireGetId,
                    'question_id' => $question['id'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Assessment created successfully",
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }


    public function updateQuestionnaire($questionnaire_id, Request $request)
    {
        try {
            // dd($request->all());
            $request->validate([
                'name' => ['required', 'string'],
                'instructions' => ['max:400', 'string'],
                'assessment_id' => ['required', 'exists:assessments,id'],
                'contacts' => ['required'],
            ], [
                // Custom error messages
                'name.required' => __('third_party.assessmentName') . " " . __('third_party.required validation'),
                'name.string' => __('third_party.assessmentName') . " " . __('third_party.string validation'),

                'instructions.string' => __('third_party.Instructions') . " " . __('third_party.string validation'),
                'instructions.max' => __('third_party.Instructions') . " " . __('third_party.max validation') . " 400 " . __("third_party.character"),

                'assessment_id.required' => __('assessment.Assessment') . " " . __('third_party.required validation'),

                'contacts.required' => __('third_party.Contacts') . " " . __('third_party.required validation'),
            ]);

            DB::beginTransaction();

            ThirdPartyQuestionnaire::where('id', $questionnaire_id)->update([
                'name' => $request->name,
                'instructions' => $request->instructions,
                'assessment_id' => $request->assessment_id,
                'all_questions_mandatory' => $request->all_questions_mandatory,
            ]);

            if (!empty($request->contacts)) {
                // Ensure it's always treated as an array for consistent handling.
                $contacts = is_array($request->contacts) ? $request->contacts : [$request->contacts];

                foreach ($contacts as $contact) {
                    ThirdPartyContactQuestionnaire::where('questionnaire_id', $questionnaire_id)->update([
                        'contact_id' => $contact,
                    ]);
                }
            }

            ThirdPartyQuestionnaireQuestion::where('questionnaire_id', $questionnaire_id)->delete();

            foreach ($request->questions as $question) {
                ThirdPartyQuestionnaireQuestion::create([
                    'questionnaire_id' => $questionnaire_id,
                    'question_id' => $question['id'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Assessment updated successfully",
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function viewQuestionnaire(Request $request, $questionnaire_id)
    {
        if ($request->ajax()) {

            $questionnaire = ThirdPartyQuestionnaire::findOrFail($questionnaire_id);

            $questionnaireContactsIds = ThirdPartyContactQuestionnaire::where('questionnaire_id', $questionnaire_id)->pluck('contact_id');
            $contactsData = ThirdPartyProfileContact::whereIn('id', $questionnaireContactsIds)->select(['name', 'email', 'phone'])->get();

            $questionnaireQuestionsIds = ThirdPartyQuestionnaireQuestion::where('questionnaire_id', $questionnaire_id)->pluck('question_id');
            $questions = Question::whereIn('id', $questionnaireQuestionsIds)->pluck('question');
            // dd($questions);

            $data = [
                'questionnaire' => $questionnaire,
                'contacts' => $contactsData,
                'questions' => $questions
            ];

            // dd($data);
            return view('admin.content.third_party.assessments.view', compact('data'));
        } else {
            abort('403');
        }
    }

    public function sendEmail(Request $request)
    {
        try {
            $questionnaire = ThirdPartyQuestionnaire::findOrFail($request->questionnaire_id);
            $questionnaireContacts = ThirdPartyContactQuestionnaire::where('questionnaire_id', $request->questionnaire_id)->get();
            // dd($questionnaireContacts);

            DB::beginTransaction();

            foreach ($questionnaireContacts->toArray() as $contact) {
                $questionnaireContactEmail = ThirdPartyProfileContact::where('id', $contact['contact_id'])->value('email');
                $questionnaireContactName = ThirdPartyProfileContact::where('id', $contact['contact_id'])->value('name');
                $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_$@-=.!~?+';
                $randomString = substr(str_shuffle(str_repeat($characters, 5)), 0, 15);

                $questionnaire_contacts[] = [
                    'id' => $contact['contact_id'],
                    'name' => $questionnaireContactName,
                    'email' => $questionnaireContactEmail,
                    'access_password' => $randomString,
                ];
                // dd($questionnaire_contacts);

                ThirdPartyContactQuestionnaireAnswer::create([
                    'questionnaire_id' => $contact['questionnaire_id'],
                    'contact_id' => $contact['contact_id'],
                    'approved_status' => null,
                    'access_password' => $randomString,
                    'send_date' => now()
                ]);
            }

            foreach ($questionnaire_contacts as $questionnaire_contact) {
                $contactEmail = $questionnaire_contact['email'];
                $body = new SendEmailToThirdPartyQuestionnaireContact($questionnaire, $questionnaire_contact);
                $bodyContent = $body->render();

                $this->pushEmail($contactEmail, $bodyContent);
            }

            DB::commit();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'errors' => $th->errors(),
            ], 422);
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

    public function viewAnswer(Request $request, $questionnaire_id)
    {
        try {
            $questionnaire_id = decrypt($questionnaire_id);
        } catch (Exception $exception) {
            abort(403);
        }

        $questionnaire = ThirdPartyQuestionnaire::findOrFail($questionnaire_id);

        $questionnaireQuestionsIds = ThirdPartyQuestionnaireQuestion::where('questionnaire_id', $questionnaire_id)->pluck('question_id');

        $questions = Question::with('answers:id,question_id,answer') // Include 'question_id' for the relationship
            ->whereIn('id', $questionnaireQuestionsIds)
            ->select('id', 'question')
            ->get();

        $questionnaireContact = ThirdPartyContactQuestionnaire::where('third_party_contact_questionnaire.questionnaire_id', $questionnaire_id)
            ->join('third_party_profile_contact', 'third_party_profile_contact.id', '=', 'third_party_contact_questionnaire.contact_id')
            ->select(
                'third_party_profile_contact.id as contact_id',
                'third_party_profile_contact.email as contact_email'
            );

        // dd($questions->toArray());

        $latestAnswer = ThirdPartyContactQuestionnaireAnswer::where('contact_id', $questionnaireContact->value('contact_id'))
            ->where('questionnaire_id', $questionnaire_id)
            ->latest()
            ->first();


        // Check if the latest submission type is "complete"
        if ($latestAnswer && $latestAnswer->submission_type == 'complete') {
            abort(403, 'You cannot access this view because your assessment is already completed.');
        } else {
            $latestResults = ThirdPartyContactQuestionnaireAnswerResult::where('third_party_contact_questionnaire_answer_result.contact_questionnaire_answer_id', $latestAnswer->id)
                ->join('questions', 'questions.id', '=', 'third_party_contact_questionnaire_answer_result.question_id')
                ->select(
                    'third_party_contact_questionnaire_answer_result.question_id as question_id',
                    'questions.question as question',
                    'third_party_contact_questionnaire_answer_result.answer_id as answer_id',
                    'third_party_contact_questionnaire_answer_result.comment as comment'
                )
                ->get(); // Retrieve the results as a collection

            // Attach the answers to each result
            $latestResultsWithAnswers = $latestResults->map(function ($result) {
                $answers = AssessmentAnswer::where('question_id', $result->question_id)->pluck('answer', 'id');
                $result->answers = $answers; // Add answers to each result
                return $result;
            });
        }

        $data = [
            'questionnaire' => $questionnaire,
            'questions' => $questions,
            'contact' => $questionnaireContact,
            'latestResults' => $latestResultsWithAnswers,
            'access_password' => $latestAnswer->access_password,
            'remedationNote' => $latestAnswer->note ?? '',
        ];

        // dd($latestResultsWithAnswers->toArray());

        return view('admin.content.third_party.assessments.answer', compact('data'));
    }

    public function saveAnswers(Request $request, $questionnaire_id)
    {
        try {
            // dd($request->all());
            $totalAnswers = count($request->answers);

            $answeredCount = collect($request->answers)
                ->filter(function ($answer) {
                    return !is_null($answer['answer_id']); // Keep only answers with a non-null answer_id
                })->count();

            $percentageComplete = $answeredCount / $totalAnswers * 100;

            // dd($request->answers);

            $latestAnswer = ThirdPartyContactQuestionnaireAnswer::where('contact_id', $request->contact_id)
                ->where('questionnaire_id', $questionnaire_id)
                ->latest()
                ->first();

            if ($request->submission_type === "complete") {

                $questionnaireRequiredCheck = ThirdPartyQuestionnaire::where('id', $questionnaire_id)->value('all_questions_mandatory');

                $validator = Validator::make($request->all(), [
                    'answers.*.answer_id' => 'required',
                ], [
                    'answers.*.answer_id.required' => 'Please answer all questions.',
                ]);

                if ($questionnaireRequiredCheck == 1 && $validator->fails()) {
                    return response()->json([
                        'message' => 'Validation error',
                        'errors' => $validator->errors(),
                    ], 422);
                } else {

                    DB::beginTransaction();

                    $latestAnswer->update([
                        'submission_type' => "complete",
                        "status" => "complete",
                        "percentage_complete" => $percentageComplete,
                        'submission_date' => now()
                    ]);

                    foreach ($request->answers as $answer) {
                        ThirdPartyContactQuestionnaireAnswerResult::updateOrCreate(
                            [
                                'contact_questionnaire_answer_id' => $latestAnswer->id,
                                'question_id' => $answer['question_id']
                            ],
                            [
                                'answer_id' => $answer['answer_id'],
                                'comment' => $answer['comment']
                            ]
                        );
                    }

                    $riskAnswers = collect($request->answers)->map(function ($answer) {
                        return AssessmentAnswer::where('id', $answer['answer_id'])
                            ->where('submit_risk', 1)
                            ->get();
                    })->collapse();

                    // dd($riskAnswers->toArray());

                    ThirdPartyQuestionnaireRisk::query()->where('questionnaire_id', $questionnaire_id)->delete();

                    // insert answers which arise risk
                    foreach ($riskAnswers as $answer) {
                        $riskScore = $answer->likelihood_id * $answer->impact_id;

                        ThirdPartyQuestionnaireRisk::query()->create([
                            'questionnaire_id' => $questionnaire_id,
                            'answer_id' => $answer->id,
                            'risk_subject' => $answer->risk_subject,
                            'risk_scoring_method_id' => $answer->risk_scoring_method_id,
                            'likelihood_id' => $answer->likelihood_id,
                            'impact_id' => $answer->impact_id,
                            'risk_score' => $riskScore,
                            'owner_id' => $answer->owner_id,
                            'assets_ids' => $answer->assets_ids,
                            'tags_ids' => $answer->tags_ids,
                            'framework_controls_ids' => $answer->framework_controls_ids,
                        ]);
                    }


                    $message = 'Answers completed successfully';

                    DB::commit();
                }
            } elseif ($request->submission_type === "draft") {
                DB::beginTransaction();

                $latestAnswer->update([
                    'submission_type' => "draft",
                    "status" => "incomplete",
                    "percentage_complete" => $percentageComplete,
                ]);

                foreach ($request->answers as $answer) {
                    ThirdPartyContactQuestionnaireAnswerResult::updateOrCreate(
                        [
                            'contact_questionnaire_answer_id' => $latestAnswer->id,
                            'question_id' => $answer['question_id'],
                        ],
                        [
                            'answer_id' => $answer['answer_id'],
                            'comment' => $answer['comment'],
                        ]
                    );
                }
                $message = 'Answers drafted successfully';

                DB::commit();
            }




            return response()->json([
                'message' => $message,
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function deleteQuestionnaire($questionnaire_id)
    {
        try {
            DB::beginTransaction();

            ThirdPartyContactQuestionnaire::where('questionnaire_id', $questionnaire_id)->delete();
            ThirdPartyQuestionnaireQuestion::where('questionnaire_id', $questionnaire_id)->delete();
            ThirdPartyQuestionnaire::findOrFail($questionnaire_id)->delete();

            DB::commit();
            return response()->json([
                'message' => 'Assessment deleted successfully',
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
