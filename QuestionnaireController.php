<?php

namespace App\Http\Controllers\admin\assessment;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionnaireRequest;
use App\Mail\SendEmailToQuestionnaireContact;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\Asset;
use App\Models\ContactQuestionnaireAnswer;
use App\Models\ContactQuestionnaireAnswerResult;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionnaireRisk;
use App\Traits\UpoladFileTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Events\QuestionnaireCreated;
use App\Events\QuestionnaireUpdated;
use App\Events\QuestionnaireDeleted;
use App\Jobs\SendEmailToContacts;
use App\Models\User;
use App\Models\Action;
use App\Models\ControlMailContent;
use Exception;
use Illuminate\Support\Facades\Crypt;
use PHPMailer\PHPMailer\PHPMailer;

class QuestionnaireController extends Controller
{
    use UpoladFileTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $breadcrumbs = [
            ['link' => route('admin.dashboard'), 'name' => __('locale.Dashboard')],
            ['name' => __('locale.Assessments')],
            ['name' => __('locale.Questionnaires')],
        ];
        if (!auth()->user()->hasPermission('assessment.list')) {
            abort(403, 'Unauthorized action.');
        }
        $assessments = Assessment::query()->with('questions:id,question')->select('name', 'id')->latest('id')->get();
        $users = DB::table('users')->select('id', 'username')->get();
        return view('admin.content.assessment.questionnaires.index', compact('breadcrumbs'), ['assessments' => $assessments, 'users' => $users]);
    }

    /**
     * @return JsonResponse
     */
    public function data(): \Illuminate\Http\JsonResponse
    {
        $questionnaires = Questionnaire::query()->with('assessment:id,name', 'contacts:id,name')->select(['id', 'name', 'assessment_id'])->latest('id');

        return DataTables::eloquent($questionnaires)
            ->addIndexColumn()
            ->skipTotalRecords()
            ->addColumn('actions', function ($questionnaire) {
                // Check if contacts id matches the authenticated user's id
                if ($questionnaire->contacts->contains('id', auth()->user()->id) || auth()->user()->hasPermission('assessment.create')) {
                    // Initialize an empty string to hold the dropdown menu items
                    $dropdownItems = '';

                    $questionnaire = Questionnaire::query()
                        ->with(['latestAnswers.results', 'assessment.questions.answers'])
                        ->findOrFail($questionnaire->id);



                    $latestAnswer = ContactQuestionnaireAnswer::where('contact_id', auth()->id())
                        ->where('questionnaire_id', $questionnaire->id)
                        ->latest()
                        ->first();

                    // Check for 'assessment.Edit' permission
                    if (auth()->user()->hasPermission('assessment.Edit') && !($questionnaire->latestAnswers && $questionnaire->latestAnswers->results->count() > 0)) {
                        $dropdownItems .= '<a href="javascript:void(0)" class="dropdown-item btn-flat-warning edit_questionnaire_btn"
                                            data-url="' . route('admin.questionnaires.edit', $questionnaire->id) . '"
                                            data-id="' . $questionnaire->id . '">
                                            <i class="fa fa-edit fa-sm"></i> ' . __('locale.Edit') . '
                                        </a>';
                    }

                    // Check for 'assessment.Delete' permission
                    if (auth()->user()->hasPermission('assessment.Delete') && !($questionnaire->latestAnswers && $questionnaire->latestAnswers->results->count() > 0)) {
                        $dropdownItems .= '<a href="' . route('admin.answers.index', $questionnaire->id) . '"
                                            class="dropdown-item btn-flat-danger delete_questionnaires_btn"
                                            data-id="' . $questionnaire->id . '"
                                            data-url="' . route('admin.questionnaires.destroy', $questionnaire->id) . '">
                                            <i class="fa fa-close fa-sm"></i> ' . __('locale.Delete') . '
                                        </a>';
                    }

                    // Check for 'assessment.Send' permission
                    if (auth()->user()->hasPermission('assessment.Send')) {
                        $dropdownItems .= '<a href="javascript:void(0)" class="dropdown-item btn-flat-secondary send_email_btn"
                                            data-id="' . $questionnaire->id . '"
                                            data-url="' . route('admin.questionnaires.sendEmail') . '">
                                            <i class="fa fa-paper-plane fa-sm"></i> ' . __('locale.Send') . '
                                        </a>';
                    }

                    // Check for 'assessment.showOption' permission
                    $showOptionDropdown = auth()->user()->hasPermission('assessment.showOption');

                    // Return the HTML content for the dropdown menu
                    return '<div class="d-inline-flex">' . ($showOptionDropdown ? '<a class="pe-1 dropdown-toggle hide-arrow text-primary" data-bs-toggle="dropdown" aria-expanded="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-more-vertical font-small-4">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <circle cx="12" cy="5" r="1"></circle>
                                            <circle cx="12" cy="19" r="1"></circle>
                                        </svg>
                                    </a>' : '') . '
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                        ' . $dropdownItems . '
                                    </div>
                                </div>';
                } else {
                    // Return an empty string for the "actions" column
                    return '';
                }
            })
            ->rawColumns(['actions'])
            ->toJson();
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuestionnaireRequest $request)
    {
        $basic_data = $request->safe()->except(['contacts', 'questions']);


        $questions = $request->questions ?? [];
        $contacts = $request->contacts ?? [];
        try {
            DB::beginTransaction();
            $questionnaire = Questionnaire::query()->create($basic_data);
            $questionnaire->contacts()->attach($contacts);
            $questionnaire->questions()->attach($questions);
            DB::commit();
            event(new QuestionnaireCreated($questionnaire));
            $message = __('assessment.A questionnaire Added with name') . ' "' . ($questionnaire->name ?? __('locale.[No Name]')) . '" '
                . __('assessment.and the instruction is') . ' "' . ($questionnaire->instructions ?? __('locale.[No Instructions]')) . '" '
                . __('assessment.and the assessment is') . ' "' . ($questionnaire->assessment->name ?? __('locale.[No Assessment]')) . '" '
                . __('assessment.and the contact is') . ' "' . (implode(", ", $questionnaire->contacts()->pluck('users.name')->toArray()) ?? __('locale.[No Contacts]')) . '" '
                . __('locale.CreatedBy') . ' "' . (auth()->user()->name ?? '[No User]') . '".';
            write_log(1, auth()->id(), $message, 'Creating questionnaire');
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $exception) {
            abort(403);
        }
        $userQuestionnaires = auth()->user()->questionnaires;

        if (!$userQuestionnaires->contains('id', $id)) {
            abort(403);
        }

        $questionnaire = Questionnaire::query()
            ->with(['latestAnswers.results', 'assessment.questions.answers'])
            ->findOrFail($id);

        $latestAnswer = ContactQuestionnaireAnswer::where('contact_id', auth()->id())
            ->where('questionnaire_id', $questionnaire->id)
            ->latest()
            ->first();

        // Check if the latest submission type is "complete"
        if ($latestAnswer && $latestAnswer->submission_type == 'complete') {
            abort(403, 'You cannot access this view because your assessment is already completed.');
        }

        $assets = Asset::query()->get(['id', 'name']);

        return view('admin.content.assessment.questionnaires.answer', compact('questionnaire', 'assets'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $questionnaire = Questionnaire::query()->find($id);
        $questionnaire->load('assessment:id', 'contacts:id', 'questions');
        return response()->json($questionnaire);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(QuestionnaireRequest $request, Questionnaire $questionnaire)
    {


        $basic_data = $request->safe()->except(['contacts', 'questions']);
        $questions = $request->questions ?? [];
        $contacts = $request->contacts ?? [];
        $basic_data['answer_percentage'] = $request->answer_percentage ?? 0;
        $basic_data['percentage_number'] = $request->percentage_number ?? '';
        $basic_data['specific_mandatory_questions'] = $request->specific_mandatory_questions ?? 0;
        $basic_data['all_questions_mandatory'] = $request->all_questions_mandatory ?? 0;


        try {
            DB::beginTransaction();
            $questionnaire->update($basic_data);
            $questionnaire->contacts()->sync($contacts);
            if ($questions != null) {
                $questionnaire->questions()->sync($questions);
            } else {
                $questionnaire->questions()->detach();
            }

            DB::commit();
            event(new QuestionnaireUpdated($questionnaire));
            $message = __('assessment.A questionnaire with name') . ' "' . ($questionnaire->name ?? __('locale.[No Name]')) . '" ' . __('locale.UpdatedBy') . ' "' . (auth()->user()->name ?? '[No User]') . '".';
            write_log(1, auth()->id(), $message, 'Updating questionnaire');
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $questionnaire = Questionnaire::query()->find($id);
        $contacts = $questionnaire->contacts()->get(); // Fetch the contacts before detachment and deletion
        $questionnaire->questions()->detach();
        $questionnaire->contacts()->detach();
        $questionnaire->delete();
        DB::commit();
        event(new QuestionnaireDeleted($questionnaire, $contacts));
        $message = __('assessment.A questionnaire with name') . ' "' . ($questionnaire->name ?? __('locale.[No Name]')) . '" ' . __('locale.DeletedBy') . ' "' . (auth()->user()->name ?? '[No User]') . '".';
        write_log(1, auth()->id(), $message, 'deleting questionnaire');
    }

    public function sendEmail(Request $request)
    {

        $request->validate([
            'questionnaire_id' => ['required', 'exists:questionnaires,id']
        ]);
        $questionnaire = Questionnaire::query()->with('contacts:id,email,name')->find($request->questionnaire_id);


        if (!empty($questionnaire->assessment->questions)) {
            $UnAnsweredQuestionsCount = Question::query()->where('answer_type', '!=', 3)->whereDoesntHave('answers')->whereIn('id', $questionnaire->assessment->questions->pluck('id')->toArray())->count();
            if ($UnAnsweredQuestionsCount > 0) {
                return response()->json('Be Sure That All Questions Have At Least One Answer', 400);
                // return response()->json(__('assessment.Be Sure That All Questions Have At Least One Answer'), 400);

            }
        }

        foreach ($questionnaire->contacts as $contact) {
            ContactQuestionnaireAnswer::query()->where([['questionnaire_id', $questionnaire->id], ['contact_id', $contact->id]]);
            ContactQuestionnaireAnswer::query()->create([
                'questionnaire_id' => $questionnaire->id,
                'contact_id' => $contact->id,
            ]);
        }
        $questionnaire_contacts = $questionnaire->contacts;
        /*dispatch(new SendEmailToContacts($questionnaire_contacts_emails, $questionnaire));*/


        foreach ($questionnaire_contacts as $questionnaire_contact) {
            $userId = $questionnaire_contact->email;
            $userName = $questionnaire_contact->email;
            // $body = new SendEmailToQuestionnaireContact($questionnaire, $questionnaire_contact);
            // $bodyContent = $body->render();
            // Get the email body content
            $type = "assessment_type";
            $bodyContent = $this->BodyHandiling($type, $questionnaire, $userName);
            $subject = ControlMailContent::where('type', $type)->value('subject');
            $this->sendEmail1($userId, $bodyContent, $questionnaire, $subject);
        }
    }

    public function BodyHandiling($type, $questionnaire, $userName)
    {
        // Retrieve the content from the database
        $mailContent = ControlMailContent::where('type', $type)->first();

        if ($mailContent) {
            // Get the content from the retrieved record
            $content = $mailContent->content;

            // Replace {name} with the actual name
            $content = str_replace('{name}', $questionnaire->name, $content);
            $content = str_replace('{user}', $userName, $content);

            // Create the button HTML
            $buttonHtml = '<a style="
             display: inline-block;
             padding: 5px 20px;
             font-size: 13px;
             text-align: center;
             text-decoration: none;
             background-color: #0097a7;
             color: #fff;
             border: 2px solid #0097a7;
             border-radius: 10px;"
             href="' . route('admin.questionnaires.view', encrypt($questionnaire->id)) . '">Show Assessment</a>';

            // Replace {link} with the button HTML
            $content = str_replace('{link}', $buttonHtml, $content);

            // Return the final content
            return $content;
        } else {
            // Handle case where no content is found for the given type
            return 'No content found for this type.';
        }
    }


    public function sendEmail1($userId, $bodyContent, $questionnaire, $subject)
    {
        $email_to = $userId; // Assumes $userId is an email address, modify if it's a user ID instead
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
            $mail->addAddress($email_to);
            $mail->setFrom($email_config->smtp_username, $email_config->smtp_from_username);

            $header = '
            <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom:1px solid #DDD; padding-bottom:10px;">
                <tr>
                    <td style="text-align: center; vertical-align: middle;">
                        <h2 style="font-family: Arial, sans-serif; color: #333; margin: 0;">GRC Platform </h2>

                    </td>
                </tr>
            </table>';

            $subjectStyled = '
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
                <tr>
                    <td style="text-align: center; font-family: Arial, sans-serif; font-size: 20px; font-weight: bold;">
                        <br>
                        ' . $subject . '
                    </td>
                </tr>
            </table>';

            $bodyStyled = '
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
                        <tr>
                            <td style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; padding: 20px;">
                                ' . $bodyContent . '
                            </td>
                        </tr>
                    </table>';

            $footer = '
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
                    <tr>
                        <td style="text-align: center;">
                            <a href="' . route('admin.questionnaires.view', encrypt($questionnaire->id)) . '"
                            style="display: inline-block; padding: 12px 24px; background-color: #0097a7;
                            color: white; text-decoration: none; border-radius: 5px; font-weight: bold;
                            font-size: 13px;">Go to Assessment</a>
                        </td>
                    </tr>
                </table>';


            $emailBody = '
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f8f8; padding: 20px;">
                    <tr>
                        <td>
                            <table width="550" cellpadding="0" cellspacing="0" style="margin: 0 auto; background-color: #ffffff; border: 1px solid #DDD; padding: 20px;">
                                <tr>
                                    <td>
                                        ' . $header . '
                                        ' . $subjectStyled . '
                                        ' . $bodyStyled . '
                                        ' . $footer . '
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>';




            $mail->Body = $emailBody;
            $mail->Subject = $subject;

            // SMTP options to disable SSL peer verification
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                )
            );

            // Send the email and return the appropriate response
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





    public function answer(Request $request)
    {
        $rules = [
            'contact_id' => ['required', 'exists:users,id'],
            'questionnaire_id' => ['required', 'exists:questionnaires,id'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_is_required' => ['required', 'in:true,false'],
            'questions.*.file' => ['sometimes', 'nullable', 'file', 'max:12288'],
            'questions.*.comment' => ['sometimes', 'nullable', 'max:1000']
        ];

        // Add the required_if rule only if the submission is not a draft
        if ($request->submission_type != 'draft') {
            $rules['questions.*.answers'] = ['required_if:questions.*.question_is_required,=,true'];
        }

        $answers_count = 0;

        foreach (request()->questions as $question) {
            if (array_key_exists('answers', $question) && $question['answers'] != null) {
                $answers_count++;
            }
        }

        $answer_percentage = ceil(($answers_count / count(request()->questions)) * 100);

        if ($request->submission_type != 'draft' && request()->answer_percentage == 1) {
            if ($answer_percentage < request('percentage_number')) {
                $rules['answer_percentage'] = ['required', 'gte: ' . request('percentage_number') . ' %'];
            }
        }

        if ($request->submission_type != 'draft') {
            $validator = Validator::make(
                $request->all(),
                $rules,
                [
                    'questions.*.answers.required_if' => 'Please answer all questions with [*] sign.'
                ],
                [
                    'questions.*.answers' => 'The Answer field'
                ]
            );

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()->toArray()], 422);
            }
        }

        // Initialize an empty array to store custom error messages for file validation
        $customErrorMessages = [];

        foreach ($request->questions as $key => $question) {
            // Check if 'answers' key exists before accessing it
            $answers = isset($question['answers']) ? $question['answers'] : [];

            if (isset($question['answer_type']) && $question['answer_type'] == 1 && isset($request->submission_type) && $request->submission_type == "complete") {
                $assessmentAnswers = DB::table('assessment_answers')
                    ->where('question_id', $question['question_id'])
                    ->orderBy('id', 'asc')
                    ->get();

                $arrangedAnswers = $assessmentAnswers->values(); // Re-index the answers

                // Iterate through the arranged answers and apply validation logic
                foreach ($arrangedAnswers as $arrangedAnswer) {
                    if (in_array($arrangedAnswer->id, (array) $answers)) {

                        if ($arrangedAnswer->attached == 1) {
                            // Add file validation rule
                            $rules["questions.$key.file"][] = 'required';
                            $rules["questions.$key.file"][] = 'file';
                            $rules["questions.$key.file"][] = 'max:12288';

                            // Add custom error message for this specific question index
                            $customErrorMessages["questions.$key.file.required"] = "File is required for question " . (($key - 1) + 1);
                        } else {
                            $rules["questions.$key.file"][] = 'sometimes';
                            $rules["questions.$key.file"][] = 'nullable';
                            $rules["questions.$key.file"][] = 'file';
                            $rules["questions.$key.file"][] = 'max:12288';
                        }
                    }
                }
            }

            // Handle case for $question['answer_type'] == 2
            elseif (isset($question['answer_type']) && $question['answer_type'] == 2 && isset($request->submission_type) && $request->submission_type == "complete") {
                $assessmentAnswers = DB::table('assessment_answers')
                    ->where('question_id', $question['question_id'])
                    ->where('attached', 1) // Only consider answers with 'attached' = 1
                    ->orderBy('id', 'asc')
                    ->get();
                $arrangedAnswers = $assessmentAnswers->values(); // Re-index the answers

                // Get array of IDs from arranged answers
                $arrangedAnswerIds = $arrangedAnswers->pluck('id')->toArray();

                // Check if any of the arranged answer IDs are in the $question['answers'] array
                foreach ($answers as $answerId) {
                    if (in_array($answerId, $arrangedAnswerIds)) {
                        // If the answer ID is in the arranged answers and no file is provided
                        if (!isset($question['file']) || empty($question['file'])) {
                            // Add file validation rule
                            $rules["questions.$key.file"][] = 'required';
                            $rules["questions.$key.file"][] = 'file';
                            $rules["questions.$key.file"][] = 'max:12288';

                            // Add custom error message for this specific question index
                            $customErrorMessages["questions.$key.file.required"] = "File is required for question " . (($key - 1) + 1);
                        }
                    }
                }
            }
        }

        // Now proceed to validation (add custom error messages)
        $validator = Validator::make(
            $request->all(),
            $rules,
            array_merge(
                [
                    'questions.*.answers.required_if' => 'Please answer all questions with [*] sign.',
                ],
                $customErrorMessages
            ),
            [
                'questions.*.answers' => 'The Answer field',
                'questions.*.file' => 'The File field',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()->toArray()], 422);
        }
        // $contact_questionnaire_answers_complete = ContactQuestionnaireAnswer::query()->where('contact_id', $request->contact_id)->where('questionnaire_id', $request->questionnaire_id)->where('submission_type', 'complete')->exists();
        // if ($contact_questionnaire_answers_complete) {
        //     return redirect()->back()->with('error', 'you  have answered this questionnaire before !');
        // }
        $questionnaire_answer = ContactQuestionnaireAnswer::query()->where([['questionnaire_id', $request->questionnaire_id], ['contact_id', $request->contact_id]])->latest()->first();

        $status = $request->submission_type == 'complete' ? 'complete' : "incomplete";


        $basic_data = $request->only(['asset_name', 'questionnaire_id', 'contact_id', 'asset_id', 'submission_type']);
        $basic_data['percentage_complete'] = $answer_percentage;
        $basic_data['status'] = $status;


        $preparedData = [];
        $RiskAnswers = [];
        foreach ($request->questions as $question) {


            // get all  answers which raise a risk
            if ($question['answer_type'] != 3) {
                if (isset($question['answers'])) {
                    if (is_array($question['answers'])) {
                        foreach ($question['answers'] as $answer_id) {
                            $answer = AssessmentAnswer::query()->whereSubmitRisk(1)->where('id', $answer_id)->first();
                            if ($answer != null) {
                                $RiskAnswers[] = $answer;
                            }
                        }
                    } else {
                        $answer = AssessmentAnswer::query()->whereSubmitRisk(1)->where('id', $question['answers'])->first();
                        if ($answer != null) {
                            $RiskAnswers[] = $answer;
                        }
                    }
                }
            }
             // prepare  questions data
            $question = [
                'contact_questionnaire_answer_id' => '',
                'answer_type' => $question['answer_type'],
                'question_id' => $question['question_id'],
                'answer_id' => is_array(@$question['answers']) || $question['answer_type'] == 3 ? null : @$question['answers'],
                'answer' => (is_array(@$question['answers'])) ? implode(',', @$question['answers']) : ($question['answer_type'] == 3 ? @$question['answers'] : ""),
                'file' => isset($question['file']) ? $this->storeFileInStorage($question['file'], 'public/images/questionnaire_results') : null,
                'comment' => @$question['comment'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
             array_push($preparedData, $question);
        }


        try {

            $answers_data = [];
            DB::beginTransaction();

            $questionnaire_answer->update($basic_data);

            foreach ($preparedData as $preparedDatum) {
                $preparedDatum['contact_questionnaire_answer_id'] = $questionnaire_answer->id;
                $answers_data[] = $preparedDatum;
            }

            ContactQuestionnaireAnswerResult::query()->where('contact_questionnaire_answer_id', $questionnaire_answer->id)->delete();
            ContactQuestionnaireAnswerResult::query()->insert($answers_data);


            QuestionnaireRisk::query()->whereQuestionnaireId($request->questionnaire_id)->delete();
            // insert answers which arise risk
            foreach ($RiskAnswers as $answer) {
                QuestionnaireRisk::query()->create([
                    'questionnaire_id' => $request->questionnaire_id,
                    'answer_id' => $answer->id,
                    'risk_subject' => $answer->risk_subject,
                    'risk_scoring_method_id' => $answer->risk_scoring_method_id,
                    'likelihood_id' => $answer->likelihood_id,
                    'impact_id' => $answer->impact_id,
                    'owner_id' => $answer->owner_id,
                    'assets_ids' => $answer->assets_ids,
                    'tags_ids' => $answer->tags_ids,
                    'framework_controls_ids' => $answer->framework_controls_ids,
                ]);
            }


            DB::commit();
            // Return a response based on the submission type
            if ($request->input('submission_type') === 'draft') {
                // Return a JSON response for draft submission
                return response()->json(['status' => 'success', 'message' => 'Your Answers Have Been Drafted']);
            } elseif ($request->input('submission_type') === 'complete') {
                // Return a JSON response for complete submission
                return response()->json(['status' => 'success', 'message' => 'Your Answers Have Been Sent']);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'errors' => [$exception->getMessage()]], 500);
        }
        // //return redirect()->to('admin/dashboard')->with('success', 'you  have  answered the questionnaire successfully');
        // return redirect()->back()->with('success', 'you  have  answered the Assessment successfully');
    }
    public function notificationsSettingsquestionnaire()
    {

        //defining the breadcrumbs that will be shown in page
        $breadcrumbs = [
            ['link' => route('admin.dashboard'), 'name' => __('locale.Dashboard')],
            ['link' => route('admin.questionnaires.index'), 'name' => __('locale.Questionnaires')],
            ['name' => __('locale.NotificationsSettings')]
        ];
        $users = User::select('id', 'name')->where('enabled', true)->get();  // getting all users to list them in select input of users
        $moduleActionsIds = [65, 66, 67];   // defining ids of actions modules
        $moduleActionsIdsAutoNotify = [];  // defining ids of actions modules

        // defining variables associated with each action "for the user to choose variables he wants to add to the message of notification" "each action id will be the array key of action's variables list"
        $actionsVariables = [
            65 => ['Name', 'Instructions', 'Assessment', 'Answer_Percentage'],
            66 => ['Name', 'Instructions', 'Assessment', 'Answer_Percentage'],
            67 => ['Name', 'Instructions', 'Assessment', 'Answer_Percentage'],
        ];
        // defining roles associated with each action "for the user to choose roles he wants to sent the notification to" "each action id will be the array key of action's roles list"
        $actionsRoles = [
            65 => ['Questionnaire-contact' => __('assessment.QuestionnaireContact')],
            66 => ['Questionnaire-contact' => __('assessment.QuestionnaireContact')],
            67 => ['Questionnaire-contact' => __('assessment.QuestionnaireContact')],

        ];
        // getting actions with their system notifications settings, sms settings and mail settings to list them in tables
        $actionsWithSettings = Action::whereIn('actions.id', $moduleActionsIds)
            ->leftJoin('system_notifications_settings', 'actions.id', '=', 'system_notifications_settings.action_id')
            ->leftJoin('mail_settings', 'actions.id', '=', 'mail_settings.action_id')
            ->leftJoin('sms_settings', 'actions.id', '=', 'sms_settings.action_id')
            ->get([
                'actions.id as action_id',
                'actions.name as action_name',
                'system_notifications_settings.id as system_notification_setting_id',
                'system_notifications_settings.status as system_notification_setting_status',
                'mail_settings.id as mail_setting_id',
                'mail_settings.status as mail_setting_status',
                'sms_settings.id as sms_setting_id',
                'sms_settings.status as sms_setting_status',
            ]);

        $actionsWithSettingsAuto = [];

        return view('admin.notifications-settings.index', compact('breadcrumbs', 'users', 'actionsWithSettings', 'actionsVariables', 'actionsRoles', 'moduleActionsIdsAutoNotify', 'actionsWithSettingsAuto'));
    }
}
