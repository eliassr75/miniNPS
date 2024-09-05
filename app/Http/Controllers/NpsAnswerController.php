<?php

namespace App\Http\Controllers;

use App\Models\Nps;
use App\Models\NpsAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Question\Question;

class NpsAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        switch ($request->page){
            case "question":

                session()->put('response_nps', $request->all());

                $why_questions = Nps::orwhere('range', 'why')->where('visibility', 'default')->get();
                if ($why_questions):
                    return redirect()->route('why');
                else:
                    return redirect()->route('justify');

                endif;
                break;
            case "why":

                session()->put('response_why', $request->all());
                return redirect()->route('justify');

                break;
            case "justify":

                $phone = $request->phone;

                try {
                    $user = User::where('email', $request->email)->first();
                    if (!$user):
                        $user = User::create([
                            'name' => $request->name,
                            'email' => $request->email,
                            'level' => 'client',
                            'password' => Hash::make(date('Y-m-d H:i:s')),
                        ]);
                    endif;

                    if (!empty($phone)) {
                        $user->client_data()->updateOrCreate(
                            ['user_id' => $user->id],
                            ['phone' => $phone]
                        );
                    }


                    $responseNps = session('response_nps');
                    $responseWhy = session('response_why');

                    // Busca a questão apenas uma vez se o NPS estiver presente
                    if ($responseNps) {
                        $question_id = $responseNps['question'];
                        $question = Nps::find($question_id);

                        // Cria a resposta de NPS
                        $question->answers()->create([
                            'value' => $responseNps['answer'],
                            'user_id' => $user->id,
                        ]);

                        $question_id = $responseWhy['question'];
                        $question = Nps::find($question_id);
                        // Se houver 'response_why', cria a justificativa
                        if ($responseWhy && isset($responseWhy["question-$question_id"])) {
                            $question->answers()->create([
                                'answer' => $responseWhy["question-$question_id"],
                                'user_id' => $user->id,
                            ]);
                        }
                    }
                }catch (\Exception $exception){
                    return redirect()->route('justify')->with('error', $exception->getMessage());
                }


                //$cookie = Cookie::make('user_answer', 'ok', 60 * 24 * 90, null, null, true, true);
                //$cookie = Cookie::make('user_entity_token', 'ok', 60 * 24 * 90, null, null, true, true);
                return redirect()->route('finish');

                break;
            default:
                return redirect()->route('nps.index');
                break;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(NpsAnswer $npsAnswer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NpsAnswer $npsAnswer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NpsAnswer $npsAnswer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NpsAnswer $npsAnswer)
    {
        //
    }
}
