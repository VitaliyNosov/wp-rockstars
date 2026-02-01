<?php
/**
 * Register GraphQL fields and mutations for the Quiz.
 */

add_action( 'graphql_register_types', 'register_quiz_graphql_logic' );

function register_quiz_graphql_logic() {

    // --- Types ---

    register_graphql_object_type( 'QuizFieldOption', [
        'fields' => [
            'value' => [ 'type' => 'String' ],
            'label' => [ 'type' => 'String' ],
            'icon'  => [ 'type' => 'String' ],
            'image' => [ 'type' => 'String' ],
        ]
    ] );

    register_graphql_object_type( 'QuizField', [
        'fields' => [
            'type'        => [ 'type' => 'String' ],
            'label'       => [ 'type' => 'String' ],
            'name'        => [ 'type' => 'String' ],
            'placeholder' => [ 'type' => 'String' ],
            'required'    => [ 'type' => 'Boolean' ],
            'options'     => [ 'type' => [ 'list_of' => 'QuizFieldOption' ] ],
            'min'         => [ 'type' => 'String' ],
            'max'         => [ 'type' => 'String' ],
            'step'        => [ 'type' => 'String' ],
            'defaultValue' => [ 'type' => 'String' ],
            'prefix'      => [ 'type' => 'String' ],
            'suffix'      => [ 'type' => 'String' ],
            'fileTypes'   => [ 'type' => 'String' ],
            'purpose'     => [ 'type' => 'String' ],
            'content'     => [ 'type' => 'String' ],
        ]
    ] );

    register_graphql_object_type( 'QuizStep', [
        'fields' => [
            'title'       => [ 'type' => 'String' ],
            'description' => [ 'type' => 'String' ],
            'fields'      => [ 'type' => [ 'list_of' => 'QuizField' ] ],
        ]
    ] );

    register_graphql_object_type( 'QuizSettings', [
        'fields' => [
            'accentColor' => [ 'type' => 'String' ],
            'btnPrev'     => [ 'type' => 'String' ],
            'btnNext'     => [ 'type' => 'String' ],
            'btnSubmit'   => [ 'type' => 'String' ],
            'steps'       => [ 'type' => [ 'list_of' => 'QuizStep' ] ],
            'nonce'       => [ 'type' => 'String' ],
        ]
    ] );

    // --- Query ---

    register_graphql_field( 'RootQuery', 'quizSettings', [
        'type' => 'QuizSettings',
        'resolve' => function() {
            if ( ! function_exists( 'carbon_get_theme_option' ) ) return null;

            $steps_data = carbon_get_theme_option( 'quiz_structure' );
            $steps = [];
            
            if ( is_array( $steps_data ) ) {
                foreach ( $steps_data as $s ) {
                    $fields = [];
                    if ( ! empty( $s['step_fields'] ) ) {
                        foreach ( $s['step_fields'] as $f ) {
                            $opts = [];
                            if ( ! empty( $f['field_options'] ) ) {
                                foreach ( $f['field_options'] as $o ) {
                                    $opts[] = [
                                        'value' => $o['option_value'] ?? '',
                                        'label' => $o['option_label'] ?? '',
                                        'icon'  => $o['option_icon'] ?? '',
                                        'image' => (isset($o['option_image']) && is_numeric($o['option_image'])) ? wp_get_attachment_url($o['option_image']) : ($o['option_image'] ?? ''),
                                    ];
                                }
                            }
                            $fields[] = [
                                'type'        => $f['_type'] ?? '',
                                'label'       => $f['field_label'] ?? '',
                                'name'        => $f['field_name'] ?? '',
                                'placeholder' => $f['field_placeholder'] ?? '',
                                'required'    => ! empty( $f['field_required'] ),
                                'options'     => $opts,
                                'min'         => $f['field_min'] ?? '',
                                'max'         => $f['field_max'] ?? '',
                                'step'        => $f['field_step'] ?? '',
                                'defaultValue' => $f['field_default'] ?? '',
                                'prefix'      => $f['field_prefix'] ?? '',
                                'suffix'      => $f['field_suffix'] ?? '',
                                'fileTypes'   => $f['field_file_types'] ?? '',
                                'purpose'     => $f['field_purpose'] ?? '',
                                'content'     => $f['field_content'] ?? '',
                            ];
                        }
                    }
                    $steps[] = [
                        'title'       => $s['step_title'] ?? '',
                        'description' => $s['step_description'] ?? '',
                        'fields'      => $fields,
                    ];
                }
            }

            return [
                'accentColor' => carbon_get_theme_option( 'quiz_accent_color' ) ?: '#4A6CF7',
                'btnPrev'     => carbon_get_theme_option( 'quiz_btn_prev' ) ?: 'Back',
                'btnNext'     => carbon_get_theme_option( 'quiz_btn_next' ) ?: 'Next',
                'btnSubmit'   => carbon_get_theme_option( 'quiz_btn_submit' ) ?: 'Submit',
                'steps'       => $steps,
                'nonce'       => wp_create_nonce( 'quiz_nonce' ),
            ];
        }
    ] );

    // --- Mutation ---

    register_graphql_mutation( 'submitQuiz', [
        'inputFields' => [
            'answers' => [
                'type' => [ 'list_of' => 'QuizAnswerInput' ],
                'description' => 'List of answers: field name and value',
            ],
            'nonce' => [
                'type' => 'String',
                'description' => 'Security nonce',
            ],
        ],
        'outputFields' => [
            'success' => [ 'type' => 'Boolean' ],
            'message' => [ 'type' => 'String' ],
            'submissionId' => [ 'type' => 'Int' ],
        ],
        'mutateAndGetPayload' => function( $input, $context, $info ) {
            $answers = $input['answers'] ?? [];
            $nonce = $input['nonce'] ?? '';

            if ( ! wp_verify_nonce( $nonce, 'quiz_nonce' ) ) {
                return [ 'success' => false, 'message' => 'Security check failed' ];
            }

            // Transform answers list to associative array (handling multiple values for same key)
            $data = [];
            foreach ( $answers as $ans ) {
                $name = $ans['name'] ?? '';
                $val  = $ans['value'] ?? '';
                if ( isset( $data[$name] ) ) {
                    if ( ! is_array( $data[$name] ) ) {
                        $data[$name] = [ $data[$name] ];
                    }
                    $data[$name][] = $val;
                } else {
                    $data[$name] = $val;
                }
            }

            if ( function_exists( 'quiz_process_submission' ) ) {
                $res = quiz_process_submission( $data );
                return [
                    'success' => $res['success'],
                    'message' => $res['success'] ? 'Quiz submitted successfully' : ($res['message'] ?? 'Unknown error'),
                    'submissionId' => $res['submission_id'] ?? 0,
                ];
            }

            return [ 'success' => false, 'message' => 'Internal error: processor not found' ];
        }
    ] );

    register_graphql_input_type( 'QuizAnswerInput', [
        'fields' => [
            'name'  => [ 'type' => 'String' ],
            'value' => [ 'type' => 'String' ], 
        ]
    ] );
}
