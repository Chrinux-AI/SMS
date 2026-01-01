<?php
/**
 * Verdant AI Core - Unified AI Handler
 * Grok API primary + Ollama local fallback
 * NERDC curriculum-aligned for Nigerian education
 */

class VerdantAI
{
    private static $instance = null;
    private $grokApiKey;
    private $ollamaUrl;
    private $model;
    private $useOffline;

    // NERDC curriculum context for prompts
    private const NERDC_CONTEXT = "You are Verdant AI, an educational assistant for Nigerian schools following the NERDC (Nigerian Educational Research and Development Council) curriculum. You help students learn, teachers plan lessons, and parents understand their children's progress. Always align responses with Nigerian Basic Education Curriculum (BEC) and Senior Secondary Education Curriculum (SSEC). Be encouraging, clear, and culturally appropriate for Nigerian students.";

    private function __construct()
    {
        $this->grokApiKey = getenv('GROK_API_KEY') ?: '';
        $this->ollamaUrl = getenv('OLLAMA_URL') ?: 'http://localhost:11434';
        $this->model = getenv('AI_MODEL') ?: 'llama3.2:3b';
        $this->useOffline = empty($this->grokApiKey);
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
        $contextPrompt = self::NERDC_CONTEXT . "\n\n";

        if ($role === 'student' && $class) {
            $contextPrompt .= "Student is in {$class}. ";
        }
        if ($subject) {
            $contextPrompt .= "Subject focus: {$subject}. ";
        }

        $contextPrompt .= "\n\nUser ({$role}): {$prompt}\n\nVerdant AI:";

        // Try Grok first, fallback to Ollama
        if (!$this->useOffline) {
            $response = $this->callGrok($contextPrompt);
            if ($response['success']) {
                return $response;
            }
        }

        // Fallback to Ollama
        return $this->callOllama($contextPrompt);
    }

    /**
     * Call Grok API (cloud)
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
     * Call Ollama API (local/offline)
     */
    private function callOllama(string $prompt): array
    {
        $url = $this->ollamaUrl . '/api/generate';

        $data = [
            'model' => $this->model,
            'prompt' => $prompt,
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
            CURLOPT_TIMEOUT => 60
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return [
                'success' => true,
                'response' => $result['response'] ?? 'No response',
                'source' => 'ollama'
            ];
        }

        // Smart local fallback when both AI backends unavailable
        return $this->getLocalFallback($prompt);
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
                'response' => "🌿 **Verdant SMS** is Nigeria's #1 AI-powered School Management System!\n\n✅ Free to start (₦5,000/year Starter plan)\n✅ AI Learning Assistant on every page\n✅ NERDC curriculum-aligned\n✅ Multi-tenant (isolated per school)\n✅ Flutterwave Naira payments\n✅ 25+ user roles\n✅ Offline-ready with PWA\n\nBuilt by Chrinux-AI for Nigerian schools. Visit /visitor/features.php for full details!",
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

        if (strpos($prompt, 'pric') !== false || strpos($prompt, 'cost') !== false || strpos($prompt, 'plan') !== false) {
            return [
                'success' => true,
                'response' => "💰 **Verdant SMS Pricing (Naira):**\n\n🌱 **Starter:** ₦5,000/yr • 50 students • Basic AI\n🍃 **Basic Cloud:** ₦50,000/yr • 300 students • Cloud hosting\n🌳 **Pro Cloud:** ₦150,000/yr • 1,000 students • Full AI\n🏢 **Enterprise:** Custom • Unlimited • Dedicated server\n\nAll plans include NERDC alignment & 8 themes!",
                'source' => 'local'
            ];
        }

        if (strpos($prompt, 'hello') !== false || strpos($prompt, 'hi') !== false) {
            return [
                'success' => true,
                'response' => "Hello! 👋 I'm Verdant AI, your NERDC-aligned learning assistant.\n\nI can help you with:\n• 📚 Homework explanations\n• ❓ Quiz practice\n• 📋 Lesson planning (teachers)\n• 📊 Progress reports (parents)\n\nWhat would you like to learn about today?",
                'source' => 'local'
            ];
        }

        // Default helpful response
        return [
            'success' => true,
            'response' => "🌿 I'm Verdant AI! I'm here to help with Nigerian education.\n\nTry asking me about:\n• \"What is Verdant SMS?\"\n• \"Show me features\"\n• \"Pricing plans\"\n• \"Help with homework\"\n\n💡 **Tip:** For full AI power, ensure you've set up Grok API key or run Ollama locally with `ollama serve`.",
            'source' => 'local'
        ];
    }

    /**
     * Generate lesson plan (NERDC-aligned)
     */
    public function generateLessonPlan(string $class, string $subject, string $topic, int $duration = 40): array
    {
        $prompt = "Create a detailed {$duration}-minute lesson plan for {$class} {$subject} on the topic: {$topic}.

Include:
1. Learning Objectives (3-4, measurable)
2. Materials Needed
3. Introduction (5 min)
4. Main Teaching Activity (20 min)
5. Student Practice (10 min)
6. Assessment Questions (3-5)
7. Homework Assignment

Align with NERDC Nigerian curriculum standards. Use simple English suitable for Nigerian students.";

        return $this->generate($prompt, ['role' => 'teacher', 'class' => $class, 'subject' => $subject]);
    }

    /**
     * Solve homework/explain concept
     */
    public function solveHomework(string $question, string $subject, string $class = ''): array
    {
        $prompt = "A Nigerian student asks: {$question}

Subject: {$subject}
" . ($class ? "Class: {$class}" : "") . "

Please:
1. Solve/explain step-by-step
2. Use simple language
3. Give 1-2 practice problems
4. Encourage the student";

        return $this->generate($prompt, ['role' => 'student', 'class' => $class, 'subject' => $subject]);
    }

    /**
     * Generate quiz questions
     */
    public function generateQuiz(string $class, string $subject, string $topic, int $count = 5): array
    {
        $prompt = "Generate {$count} multiple-choice quiz questions for {$class} {$subject} on topic: {$topic}.

Format each question as:
Q1. [Question]
A) [Option]
B) [Option]
C) [Option]
D) [Option]
Answer: [Letter]

Align with NERDC curriculum.";

        return $this->generate($prompt, ['role' => 'teacher', 'class' => $class, 'subject' => $subject]);
    }

    /**
     * Get student progress summary (for parents)
     */
    public function getProgressSummary(array $studentData): array
    {
        $name = $studentData['name'] ?? 'Your child';
        $class = $studentData['class'] ?? '';
        $attendance = $studentData['attendance'] ?? 90;
        $avgGrade = $studentData['avg_grade'] ?? 75;
        $weakSubjects = $studentData['weak_subjects'] ?? [];

        $prompt = "Summarize this student's progress for their parent:

Student: {$name}
Class: {$class}
Attendance: {$attendance}%
Average Grade: {$avgGrade}%
Weak Areas: " . implode(', ', $weakSubjects) . "

Provide:
1. Brief summary (2-3 sentences)
2. Strengths
3. Areas to improve
4. Tips for parents to help at home

Be encouraging and culturally appropriate for Nigerian parents.";

        return $this->generate($prompt, ['role' => 'parent']);
    }
}

// Helper function for quick access
function verdant_ai(): VerdantAI
{
    return VerdantAI::getInstance();
}
