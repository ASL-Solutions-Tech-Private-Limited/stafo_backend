<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactFormSubmission;
use App\Models\CallbackRequest;
use Illuminate\Support\Facades\Log;

class WebsiteAiChatController extends Controller
{
    /**
     * Handle incoming visitor message to Nisha AI Assistant.
     */
    public function handleMessage(Request $request)
    {
        $message = trim((string) $request->input('message', ''));
        $userName = $request->input('name', null);
        $userPhone = $request->input('phone', null);
        $userEmail = $request->input('email', null);

        if (empty($message)) {
            return response()->json([
                'status' => 'error',
                'reply' => "I'm here to help! Please feel free to ask any question regarding Stafo HRMS.",
            ], 400);
        }

        // Check if message contains a phone number or email to auto-capture lead
        $phoneFound = null;
        if (preg_match('/(\+?91[\-\s]?)?[6-9]\d{9}/', $message, $matches)) {
            $phoneFound = preg_replace('/[^\d]/', '', $matches[0]);
            if (strlen($phoneFound) > 10) {
                $phoneFound = substr($phoneFound, -10);
            }
        }

        $emailFound = null;
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $message, $matches)) {
            $emailFound = strtolower($matches[0]);
        }

        if ($phoneFound || $emailFound) {
            try {
                CallbackRequest::create([
                    'name' => $userName ?: 'Website AI Chat Visitor',
                    'phone' => $phoneFound ?: ($userPhone ?: 'N/A'),
                ]);

                ContactFormSubmission::create([
                    'full_name' => $userName ?: 'Website AI Chat Visitor',
                    'email' => $emailFound ?: ($userEmail ?: 'ai-chat-lead@stafo.in'),
                    'phone' => $phoneFound ?: ($userPhone ?: 'N/A'),
                    'message' => "Captured via Chaity Chat: " . $message,
                ]);
            } catch (\Exception $e) {
                Log::warning('AI chat lead capture error: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'reply' => "Thank you! I have recorded your contact details. Our Stafo HRMS product specialist will contact you shortly on " . ($phoneFound ? "+91 $phoneFound" : $emailFound) . ". In the meantime, you can explore our features or start an instant 15-Day Free Trial!",
                'quick_actions' => [
                    ['label' => 'Start 15-Day Free Trial', 'url' => route('register'), 'type' => 'link'],
                    ['label' => 'View Pricing Plans', 'action' => 'Pricing', 'type' => 'pill'],
                    ['label' => 'Call Support (+91 6292252470)', 'url' => 'tel:+916292252470', 'type' => 'link'],
                ]
            ]);
        }

        // Process message through Stafo HRMS support knowledge engine
        $response = $this->generateSupportResponse($message);

        return response()->json([
            'status' => 'success',
            'reply' => $response['text'],
            'quick_actions' => $response['quick_actions'] ?? [],
            'suggested_links' => $response['suggested_links'] ?? []
        ]);
    }

    /**
     * Upload an attachment in AI Chat (screenshot or doc).
     */
    public function uploadAttachment(Request $request)
    {
        $request->validate([
            'attachment' => 'required|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx|max:10240',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'ai_chat_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/chat_attachments'), $filename);
            $fileUrl = asset('uploads/chat_attachments/' . $filename);

            return response()->json([
                'status' => 'success',
                'filename' => $filename,
                'url' => $fileUrl,
                'reply' => "I've received your attachment! Our support team has logged this screenshot. How can I assist you with this issue in Stafo HRMS?"
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'File upload failed.'
        ], 400);
    }

    /**
     * Stafo Support Knowledge Engine.
     */
    protected function generateSupportResponse(string $message): array
    {
        $msg = strtolower($message);

        // 1. Product Demo Intent
        if (preg_match('/(demo|product demo|schedule|presentation|book|trail|trial|walkthrough)/i', $msg)) {
            return [
                'text' => "We'd love to show you how Stafo can automate your HR and payroll operations! ✨\n\n• Live 1-on-1 walkthrough with an HRMS expert\n• Custom setup for your company size\n• 15-Day Free Trial (no credit card needed)\n\nPlease share your **Phone Number** or **Company Name**, and our team will schedule a demo immediately.",
                'quick_actions' => [
                    ['label' => 'Start 15-Day Free Trial', 'url' => route('register'), 'type' => 'link'],
                    ['label' => 'Call +91 6292252470', 'url' => 'tel:+916292252470', 'type' => 'link'],
                    ['label' => 'View Pricing', 'action' => 'Pricing', 'type' => 'pill'],
                ]
            ];
        }

        // 2. Pricing & Cost Intent
        if (preg_match('/(price|pricing|cost|plan|rate|subscription|charge|fee|quote|discount)/i', $msg)) {
            return [
                'text' => "Stafo HRMS offers flexible and highly cost-effective plans for businesses of all sizes:\n\n"
                    . "🌟 **15-Day Free Trial**: 100% full access to all features (no credit card required).\n"
                    . "💼 **All-Inclusive HRMS Suite** includes:\n"
                    . "• Automated Attendance & Mobile GPS Punch\n"
                    . "• 1-Click Payroll & Payslips with PF/ESIC/TDS compliance\n"
                    . "• Real-Time Field Employee Location Tracking\n"
                    . "• Task Management & Performance Dashboard\n"
                    . "• 24/7 Dedicated Support\n\n"
                    . "Would you like our sales team to provide a customized quote for your team size?",
                'quick_actions' => [
                    ['label' => 'View Pricing Page', 'url' => route('price'), 'type' => 'link'],
                    ['label' => 'Start Free Trial', 'url' => route('register'), 'type' => 'link'],
                    ['label' => 'Talk to Sales', 'action' => 'Support', 'type' => 'pill'],
                ]
            ];
        }

        // 3. Attendance & Biometric Integration Intent
        if (preg_match('/(attendance|biometric|fingerprint|face id|zkteco|punch|clock|geofence|gps|leave|shift|overtime|late)/i', $msg)) {
            return [
                'text' => "Stafo provides an end-to-end Attendance Management solution:\n\n"
                    . "🔹 **Biometric Machine Sync**: Seamlessly connects with ZKTeco, Essl, Realtime & other popular biometric & face recognition machines.\n"
                    . "🔹 **Mobile App Punch**: Geo-fenced mobile attendance with GPS location and selfie verification.\n"
                    . "🔹 **Automated Policies**: Configurable shift rosters, late mark grace periods, half-day rules, and overtime calculations.\n"
                    . "🔹 **Leave Management**: Casual, Sick, Earned, Comp-off leave balances and approval workflows.",
                'quick_actions' => [
                    ['label' => 'Book Product Demo', 'action' => 'Product Demo', 'type' => 'pill'],
                    ['label' => 'Payroll Features', 'action' => 'Payroll', 'type' => 'pill'],
                    ['label' => 'Contact Support', 'action' => 'Support', 'type' => 'pill'],
                ]
            ];
        }

        // 4. Payroll & Compliance Intent
        if (preg_match('/(payroll|salary|payslip|pf|esic|tds|tax|form 16|bonus|deduction|bank transfer|neft)/i', $msg)) {
            return [
                'text' => "Stafo's automated 1-Click Payroll engine takes the stress out of month-end salary processing:\n\n"
                    . "💵 **Instant Salary Generation**: Auto-calculates earnings based on approved biometric attendance & leaves.\n"
                    . "⚖️ **Statutory Compliance**: Automated deductions for Provident Fund (PF), ESIC, Professional Tax (PT), and TDS.\n"
                    . "📄 **Professional Payslips**: Auto-generates branded PDF payslips accessible by employees via mobile portal.\n"
                    . "🏦 **Bank Transfer Files**: 1-click export of batch payment files ready for HDFC, ICICI, SBI, Axis, etc.",
                'quick_actions' => [
                    ['label' => 'Start Free Trial', 'url' => route('register'), 'type' => 'link'],
                    ['label' => 'Product Demo', 'action' => 'Product Demo', 'type' => 'pill'],
                    ['label' => 'Ask for Demo Callback', 'action' => 'Support', 'type' => 'pill'],
                ]
            ];
        }

        // 5. Customer Support & Contact Info Intent
        if (preg_match('/(support|contact|help|call|phone|number|mobile|email|address|office|location|whatsapp)/i', $msg)) {
            return [
                'text' => "We are here to help you 24/7! You can reach the Stafo support team directly:\n\n"
                    . "📞 **Helpline**: [+91 6292252470](tel:+916292252470)\n"
                    . "✉️ **Email**: [stafo.sales@stafo.in](mailto:stafo.sales@stafo.in) / [support@stafo.com](mailto:support@stafo.com)\n"
                    . "📍 **Head Office**: F/28/1, Katjunagar Colony, Kolkata - 700032\n"
                    . "🕒 **Support Hours**: Mon - Sat, 9:30 AM to 7:00 PM (Emergency 24/7 assistance)\n\n"
                    . "You can also leave your phone number here, and we'll call you right back!",
                'quick_actions' => [
                    ['label' => 'Call +91 6292252470', 'url' => 'tel:+916292252470', 'type' => 'link'],
                    ['label' => 'Visit Contact Page', 'url' => route('contactUs'), 'type' => 'link'],
                    ['label' => 'Book a Demo', 'action' => 'Product Demo', 'type' => 'pill'],
                ]
            ];
        }

        // 6. Job / Careers Intent
        if (preg_match('/(job|career|hiring|recruitment|vacancy|apply|opening|cv|resume|interview)/i', $msg)) {
            return [
                'text' => "We are always looking for passionate talent to join the Stafo & ASL Solutions Tech team! 🚀\n\n"
                    . "• Check current job vacancies on our Careers page.\n"
                    . "• You can directly email your resume and portfolio to:\n"
                    . "✉️ **hr@aslsolution.in** or **support@stafo.com**\n\n"
                    . "Please mention the role you are applying for in the email subject line.",
                'quick_actions' => [
                    ['label' => 'View Careers Page', 'url' => route('carrer'), 'type' => 'link'],
                    ['label' => 'Email HR (hr@aslsolution.in)', 'url' => 'mailto:hr@aslsolution.in', 'type' => 'link'],
                    ['label' => 'About Stafo', 'url' => route('aboutUs'), 'type' => 'link'],
                ]
            ];
        }

        // 7. Partnership / Reseller / Franchise Intent
        if (preg_match('/(partner|partnership|reseller|franchise|distributor|tie up|collab|collaboration|affiliate)/i', $msg)) {
            return [
                'text' => "Join the Stafo Partner Ecosystem! 🤝\n\n"
                    . "We partner with HR Consultants, Chartered Accountants, IT Solution Providers, and System Integrators:\n"
                    . "• Attractive recurring revenue share & commissions\n"
                    . "• Dedicated partner support & client onboarding assistance\n"
                    . "• Co-branded marketing collateral\n\n"
                    . "Please drop your contact number or company email, and our Partnership Director will connect with you.",
                'quick_actions' => [
                    ['label' => 'Call +91 6292252470', 'url' => 'tel:+916292252470', 'type' => 'link'],
                    ['label' => 'Email: stafo.sales@stafo.in', 'url' => 'mailto:stafo.sales@stafo.in', 'type' => 'link'],
                ]
            ];
        }

        // 8. Field Tracking & Location Intent
        if (preg_match('/(track|tracking|live location|field|route|sales team|movement|map)/i', $msg)) {
            return [
                'text' => "Stafo includes **Real-Time GPS Location Tracking** specifically engineered for on-field sales, technicians, and delivery staff:\n\n"
                    . "📍 Live location monitoring on interactive Google Maps\n"
                    . "🛣️ Travel route history with distance (KM) calculation\n"
                    . "🔋 Battery status and GPS connection monitoring\n"
                    . "🛡️ Privacy-first: Tracking only operates during active work shift hours.",
                'quick_actions' => [
                    ['label' => 'Book Product Demo', 'action' => 'Product Demo', 'type' => 'pill'],
                    ['label' => 'View Pricing', 'action' => 'Pricing', 'type' => 'pill'],
                ]
            ];
        }

        // 9. Login, Mobile App & Account Issues
        if (preg_match('/(login|signin|sign in|password|forgot|reset|app|download|apk|android|ios)/i', $msg)) {
            return [
                'text' => "Need assistance with your Stafo account or Mobile App?\n\n"
                    . "🔐 **Web Login**: Access your company portal at [https://stafo.in/login](https://stafo.in/login).\n"
                    . "📱 **Mobile App**: Employees and managers can log attendance, apply leaves, and view payslips on the go.\n"
                    . "🔑 **Password Reset**: If you are unable to login, contact your company HR admin or reach our helpline for immediate account unlock.",
                'quick_actions' => [
                    ['label' => 'Go to Login', 'url' => route('login'), 'type' => 'link'],
                    ['label' => 'Call Support', 'url' => 'tel:+916292252470', 'type' => 'link'],
                ]
            ];
        }

        // 10. Greetings
        if (preg_match('/^(hi|hello|hey|namaste|good morning|good afternoon|good evening|chaity|nisha)/i', $msg)) {
            return [
                'text' => "Hello! 👋 I'm Chaity from the Stafo Support Team. How can I assist you with Stafo HRMS today? You can choose one of the options below or type your question directly.",
                'quick_actions' => [
                    ['label' => 'Product Demo', 'action' => 'Product Demo', 'type' => 'pill'],
                    ['label' => 'Pricing & Plans', 'action' => 'Pricing', 'type' => 'pill'],
                    ['label' => 'Attendance & Biometrics', 'action' => 'Attendance', 'type' => 'pill'],
                    ['label' => 'Support / Contact', 'action' => 'Support', 'type' => 'pill'],
                ]
            ];
        }

        // 11. Polite Fallback / Off-Topic Guardrail (Only Stafo HRMS support)
        return [
            'text' => "I am Chaity, your dedicated Stafo HRMS Assistant! 😊\n\n"
                . "I specialize strictly in **Stafo HRMS support, features, pricing, payroll, biometric attendance, and live demos**.\n\n"
                . "How can I help you regarding Stafo HRMS today?",
            'quick_actions' => [
                ['label' => 'Product Demo', 'action' => 'Product Demo', 'type' => 'pill'],
                ['label' => 'Job', 'action' => 'Job', 'type' => 'pill'],
                ['label' => 'Partnership', 'action' => 'Partnership', 'type' => 'pill'],
                ['label' => 'Support', 'action' => 'Support', 'type' => 'pill'],
            ]
        ];
    }
}
