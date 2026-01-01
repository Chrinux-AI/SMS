<?php
/**
 * Verdant AI Core - Unified AI Handler
 * Gemini API primary + Grok + Ollama fallback
 * NERDC curriculum-aligned for Nigerian education
 */

class VerdantAI
{
    private static $instance = null;
    private $geminiApiKey;
    private $grokApiKey;
    private $ollamaUrl;
    private $model;

    // NERDC curriculum context for prompts
    private const NERDC_CONTEXT = "You are Verdant AI, an educational assistant for Nigerian schools following the NERDC (Nigerian Educational Research and Development Council) curriculum. You help students learn, teachers plan lessons, and parents understand their children's progress. Always align responses with Nigerian Basic Education Curriculum (BEC) and Senior Secondary Education Curriculum (SSEC). Be encouraging, clear, and culturally appropriate for Nigerian students. Provide helpful, detailed answers.";

    private function __construct()
    {
        // Gemini API (Primary - Google AI)
        $this->geminiApiKey = getenv('GEMINI_API_KEY') ?: 'AIzaSyBBLiXbn_UBR6sTYlI_hn8JKJFz9vmbkhk';
        // Grok API (Secondary - xAI)
        $this->grokApiKey = getenv('GROK_API_KEY') ?: '';
        // Ollama (Tertiary - Local)
        $this->ollamaUrl = getenv('OLLAMA_URL') ?: 'http://localhost:11434';
        $this->model = getenv('AI_MODEL') ?: 'llama3.2:3b';
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Generate AI response with NERDC context
     */
    public function generate(string $prompt, array $options = []): array
    {
        $role = $options['role'] ?? 'student';
        $class = $options['class'] ?? '';
        $subject = $options['subject'] ?? '';

        // Build context-aware prompt
        $contextPrompt = "";
        if ($role === 'student' && $class) {
            $contextPrompt .= "Student is in {$class}. ";
        }
        if ($subject) {
            $contextPrompt .= "Subject focus: {$subject}. ";
        }
        $contextPrompt .= "\n\nQuestion: {$prompt}";

        // Try Gemini first
        if (!empty($this->geminiApiKey)) {
            $response = $this->callGemini($contextPrompt);
            if ($response['success']) {
                return $response;
            }
        }

        // Fallback to Grok
        if (!empty($this->grokApiKey)) {
            $response = $this->callGrok($contextPrompt);
            if ($response['success']) {
                return $response;
            }
        }

        // Fallback to Ollama
        $response = $this->callOllama($contextPrompt);
        if ($response['success']) {
            return $response;
        }

        // Final fallback - local responses
        return $this->getLocalFallback($prompt);
    }

    /**
     * Call Google Gemini API (primary)
     */
    private function callGemini(string $prompt): array
    {
        // Use gemini-2.0-flash (available in v1beta)
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $this->geminiApiKey;

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => self::NERDC_CONTEXT . "\n\n" . $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1024,
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $result = json_decode($response, true);
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($text) {
                return [
                    'success' => true,
                    'response' => $text,
                    'source' => 'gemini'
                ];
            }
        }

        error_log("Gemini API error: HTTP $httpCode - $error - Response: " . substr($response ?? '', 0, 500));
        return ['success' => false, 'error' => 'Gemini API failed'];
    }

    /**
     * Call Grok API (xAI - secondary)
     */
    private function callGrok(string $prompt): array
    {
        $url = 'https://api.x.ai/v1/chat/completions';

        $data = [
            'model' => 'grok-beta',
            'messages' => [
                ['role' => 'system', 'content' => self::NERDC_CONTEXT],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->grokApiKey
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return [
                'success' => true,
                'response' => $result['choices'][0]['message']['content'] ?? 'No response',
                'source' => 'grok'
            ];
        }

        return ['success' => false, 'error' => 'Grok API failed'];
    }

    /**
     * Call Ollama API (local/offline - tertiary)
     */
    private function callOllama(string $prompt): array
    {
        $url = $this->ollamaUrl . '/api/generate';

        $data = [
            'model' => $this->model,
            'prompt' => self::NERDC_CONTEXT . "\n\n" . $prompt,
            'stream' => false,
            'options' => [
                'temperature' => 0.7,
                'num_predict' => 500
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 5
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return [
                'success' => true,
                'response' => $result['response'] ?? 'No response',
                'source' => 'ollama'
            ];
        }

        return ['success' => false, 'error' => 'Ollama unavailable'];
    }

    /**
     * Smart local fallback responses
     */
    private function getLocalFallback(string $prompt): array
    {
        $prompt = strtolower($prompt);

        // Common questions about Verdant
        if (strpos($prompt, 'verdant') !== false || strpos($prompt, 'what is') !== false) {
            return [
                'success' => true,
                'response' => "🌿 **Verdant SMS** is Nigeria's #1 AI-powered School Management System!\n\n✅ From ₦5,000/year (Starter plan)\n✅ AI Learning Assistant on every page\n✅ NERDC curriculum-aligned\n✅ Multi-tenant (isolated per school)\n✅ Flutterwave Naira payments\n✅ 25+ user roles\n✅ Offline-ready with PWA\n\nBuilt by Chrinux-AI for Nigerian schools!",
                'source' => 'local'
            ];
        }

        if (strpos($prompt, 'feature') !== false) {
            return [
                'success' => true,
                'response' => "🎯 **Verdant SMS Features:**\n\n📚 **For Students:** AI homework help, quizzes, voice tutoring\n👨‍🏫 **For Teachers:** AI lesson planner, auto-grading, NERDC content\n👪 **For Parents:** Progress reports, school fees, communication\n🏫 **For Admin:** Analytics, attendance, fee management\n\n🎨 8 themes • 📱 Mobile-ready • 🔐 Secure multi-tenant",
                'source' => 'local'
            ];
        }

        if (strpos($prompt, 'price') !== false || strpos($prompt, 'cost') !== false || strpos($prompt, 'plan') !== false) {
            return [
                'success' => true,
                'response' => "💰 **Verdant SMS Pricing:**\n\n🌱 **Starter:** ₦5,000/year (50 students, basic features)\n🍃 **Standard:** ₦75,000/year (500 students, AI, reports)\n🌳 **Pro AI:** ₦180,000/year (Unlimited, full AI, voice/image)\n🏢 **Enterprise:** Custom pricing\n\nAll plans include NERDC curriculum alignment!",
                'source' => 'local'
            ];
        }

        if (strpos($prompt, 'hello') !== false || strpos($prompt, 'hi') !== false) {
            return [
                'success' => true,
                'response' => "Hello! 👋 I'm Verdant AI, your Nigerian educational assistant!\n\nI can help you with:\n📚 Homework and explanations\n📝 Quizzes and tests\n📖 Lesson planning (for teachers)\n📊 Academic progress\n\nWhat would you like to learn about today?",
                'source' => 'local'
            ];
        }

        // Generic helpful response
        return [
            'success' => true,
            'response' => "🤖 I'm Verdant AI, ready to help with your education!\n\nTry asking me:\n• \"Explain photosynthesis\"\n• \"Solve 2x + 5 = 15\"\n• \"Create a quiz on Nigerian history\"\n• \"Help me with my English essay\"\n\nI'm aligned with the NERDC Nigerian curriculum. What can I help you learn today?",
            'source' => 'local'
        ];
    }

    // ==================== EDUCATIONAL FEATURES ====================

    /**
     * Generate NERDC-aligned lesson plan
     */
    public function generateLessonPlan(string $subject, string $topic, string $class, int $duration = 40): array
    {
        $prompt = "Create a detailed NERDC-aligned lesson plan for:
Subject: {$subject}
Topic: {$topic}
Class: {$class}
Duration: {$duration} minutes

Include:
1. Learning Objectives (3-5 specific goals)
2. Required Materials/Resources
3. Introduction/Warm-up Activity (5 min)
4. Main Teaching Activities (step-by-step)
5. Student Practice Activities
6. Assessment Methods
7. Homework Assignment
8. Differentiation for slower/faster learners

Format clearly with headings.";

        return $this->generate($prompt, ['role' => 'teacher', 'subject' => $subject, 'class' => $class]);
    }

    /**
     * Help solve homework with explanations
     */
    public function solveHomework(string $question, string $subject, string $class = ''): array
    {
        $prompt = "A Nigerian student needs help with this homework:

Question: {$question}
Subject: {$subject}
" . ($class ? "Class: {$class}" : "") . "

Please:
1. Solve the problem step-by-step
2. Explain each step clearly in simple English
3. Give a final answer
4. Suggest a similar practice problem

Use Nigerian examples where relevant.";

        return $this->generate($prompt, ['role' => 'student', 'subject' => $subject, 'class' => $class]);
    }

    /**
     * Generate quiz questions
     */
    public function generateQuiz(string $subject, string $topic, string $class, int $numQuestions = 5): array
    {
        $prompt = "Create a quiz for Nigerian students:

Subject: {$subject}
Topic: {$topic}
Class: {$class}
Number of Questions: {$numQuestions}

Format each question as:
- Question text
- 4 options (A, B, C, D)
- Correct answer
- Brief explanation

Align with NERDC curriculum standards.";

        return $this->generate($prompt, ['role' => 'teacher', 'subject' => $subject, 'class' => $class]);
    }

    /**
     * Generate progress summary for parents
     */
    public function generateProgressSummary(array $studentData): array
    {
        $name = $studentData['name'] ?? 'Student';
        $attendance = $studentData['attendance'] ?? 'N/A';
        $grades = $studentData['grades'] ?? [];

        $gradeString = !empty($grades) ? implode(', ', array_map(fn($s, $g) => "$s: $g%", array_keys($grades), $grades)) : 'No grades yet';

        $prompt = "Create a parent-friendly progress summary:

Student: {$name}
Attendance Rate: {$attendance}%
Recent Grades: {$gradeString}

Include:
1. Overall performance assessment
2. Strengths observed
3. Areas needing improvement
4. Suggestions for parents to help at home
5. Encouraging message

Write in a warm, supportive tone.";

        return $this->generate($prompt, ['role' => 'parent']);
    }
}

// Quick access function
function verdant_ai(): VerdantAI
{
    return VerdantAI::getInstance();
}
