<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome Email',
                'type' => 'outgoing',
                'category' => 'Client',
                'description' => 'Welcome email sent to new clients after registration',
                'subject_en' => 'Welcome to {{company_name}}!',
                'body_en' => '<p>Dear {{client_name}},</p><p>Welcome to {{company_name}}! We are excited to have you as our client.</p><p>Your account has been successfully created. You can now login to your client area using your email address.</p><p>If you have any questions, please don\'t hesitate to contact us.</p><p>Best regards,<br>{{company_name}} Team</p>',
                'subject_ar' => 'مرحباً بك في {{company_name}}!',
                'body_ar' => '<p>عزيزي {{client_name}}،</p><p>مرحباً بك في {{company_name}}! نحن سعداء بانضمامك كعميل لدينا.</p><p>تم إنشاء حسابك بنجاح. يمكنك الآن تسجيل الدخول إلى منطقة العملاء باستخدام عنوان بريدك الإلكتروني.</p><p>إذا كان لديك أي أسئلة، لا تتردد في الاتصال بنا.</p><p>مع أطيب التحيات،<br>فريق {{company_name}}</p>',
                'subject_fr' => 'Bienvenue chez {{company_name}}!',
                'body_fr' => '<p>Cher {{client_name}},</p><p>Bienvenue chez {{company_name}}! Nous sommes ravis de vous compter parmi nos clients.</p><p>Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter à votre espace client en utilisant votre adresse e-mail.</p><p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p><p>Cordialement,<br>L\'équipe {{company_name}}</p>',
                'variables' => ['client_name', 'company_name'],
                'is_active' => true,
            ],
            [
                'name' => 'Order Confirmation',
                'type' => 'outgoing',
                'category' => 'Orders',
                'description' => 'Order confirmation email sent after successful order placement',
                'subject_en' => 'Order Confirmation - Order #{{order_id}}',
                'body_en' => '<p>Dear {{client_name}},</p><p>Thank you for your order! Your order #{{order_id}} has been received and is being processed.</p><p><strong>Order Details:</strong></p><p>Order ID: {{order_id}}<br>Order Date: {{order_date}}<br>Total Amount: {{order_total}}</p><p>You will receive another email once your order is completed.</p><p>Best regards,<br>{{company_name}} Team</p>',
                'subject_ar' => 'تأكيد الطلب - طلب رقم {{order_id}}',
                'body_ar' => '<p>عزيزي {{client_name}}،</p><p>شكراً لطلبك! تم استلام طلبك رقم {{order_id}} وجاري معالجته.</p><p><strong>تفاصيل الطلب:</strong></p><p>رقم الطلب: {{order_id}}<br>تاريخ الطلب: {{order_date}}<br>المبلغ الإجمالي: {{order_total}}</p><p>ستتلقى بريداً إلكترونياً آخر بمجرد اكتمال طلبك.</p><p>مع أطيب التحيات،<br>فريق {{company_name}}</p>',
                'subject_fr' => 'Confirmation de commande - Commande #{{order_id}}',
                'body_fr' => '<p>Cher {{client_name}},</p><p>Merci pour votre commande! Votre commande #{{order_id}} a été reçue et est en cours de traitement.</p><p><strong>Détails de la commande:</strong></p><p>ID de commande: {{order_id}}<br>Date de commande: {{order_date}}<br>Montant total: {{order_total}}</p><p>Vous recevrez un autre e-mail une fois votre commande terminée.</p><p>Cordialement,<br>L\'équipe {{company_name}}</p>',
                'variables' => ['client_name', 'company_name', 'order_id', 'order_date', 'order_total'],
                'is_active' => true,
            ],
            [
                'name' => 'Invoice Created',
                'type' => 'outgoing',
                'category' => 'Invoices',
                'description' => 'Email sent when a new invoice is generated',
                'subject_en' => 'New Invoice #{{invoice_id}} - {{invoice_total}}',
                'body_en' => '<p>Dear {{client_name}},</p><p>A new invoice has been generated for your account.</p><p><strong>Invoice Details:</strong></p><p>Invoice ID: {{invoice_id}}<br>Invoice Date: {{invoice_date}}<br>Due Date: {{due_date}}<br>Amount: {{invoice_total}}</p><p>Please login to your client area to view and pay this invoice.</p><p>Best regards,<br>{{company_name}} Team</p>',
                'subject_ar' => 'فاتورة جديدة رقم {{invoice_id}} - {{invoice_total}}',
                'body_ar' => '<p>عزيزي {{client_name}}،</p><p>تم إنشاء فاتورة جديدة لحسابك.</p><p><strong>تفاصيل الفاتورة:</strong></p><p>رقم الفاتورة: {{invoice_id}}<br>تاريخ الفاتورة: {{invoice_date}}<br>تاريخ الاستحقاق: {{due_date}}<br>المبلغ: {{invoice_total}}</p><p>يرجى تسجيل الدخول إلى منطقة العملاء لعرض ودفع هذه الفاتورة.</p><p>مع أطيب التحيات،<br>فريق {{company_name}}</p>',
                'subject_fr' => 'Nouvelle facture #{{invoice_id}} - {{invoice_total}}',
                'body_fr' => '<p>Cher {{client_name}},</p><p>Une nouvelle facture a été générée pour votre compte.</p><p><strong>Détails de la facture:</strong></p><p>ID de facture: {{invoice_id}}<br>Date de facture: {{invoice_date}}<br>Date d\'échéance: {{due_date}}<br>Montant: {{invoice_total}}</p><p>Veuillez vous connecter à votre espace client pour consulter et payer cette facture.</p><p>Cordialement,<br>L\'équipe {{company_name}}</p>',
                'variables' => ['client_name', 'company_name', 'invoice_id', 'invoice_date', 'due_date', 'invoice_total'],
                'is_active' => true,
            ],
            [
                'name' => 'Payment Received',
                'type' => 'outgoing',
                'category' => 'Invoices',
                'description' => 'Email sent when payment is received for an invoice',
                'subject_en' => 'Payment Received - Invoice #{{invoice_id}}',
                'body_en' => '<p>Dear {{client_name}},</p><p>We have received your payment for invoice #{{invoice_id}}.</p><p><strong>Payment Details:</strong></p><p>Invoice ID: {{invoice_id}}<br>Amount Paid: {{payment_amount}}<br>Payment Date: {{payment_date}}<br>Payment Method: {{payment_method}}</p><p>Thank you for your payment!</p><p>Best regards,<br>{{company_name}} Team</p>',
                'subject_ar' => 'تم استلام الدفع - فاتورة رقم {{invoice_id}}',
                'body_ar' => '<p>عزيزي {{client_name}}،</p><p>لقد استلمنا دفعتك للفاتورة رقم {{invoice_id}}.</p><p><strong>تفاصيل الدفع:</strong></p><p>رقم الفاتورة: {{invoice_id}}<br>المبلغ المدفوع: {{payment_amount}}<br>تاريخ الدفع: {{payment_date}}<br>طريقة الدفع: {{payment_method}}</p><p>شكراً لك على دفعتك!</p><p>مع أطيب التحيات،<br>فريق {{company_name}}</p>',
                'subject_fr' => 'Paiement reçu - Facture #{{invoice_id}}',
                'body_fr' => '<p>Cher {{client_name}},</p><p>Nous avons reçu votre paiement pour la facture #{{invoice_id}}.</p><p><strong>Détails du paiement:</strong></p><p>ID de facture: {{invoice_id}}<br>Montant payé: {{payment_amount}}<br>Date de paiement: {{payment_date}}<br>Méthode de paiement: {{payment_method}}</p><p>Merci pour votre paiement!</p><p>Cordialement,<br>L\'équipe {{company_name}}</p>',
                'variables' => ['client_name', 'company_name', 'invoice_id', 'payment_amount', 'payment_date', 'payment_method'],
                'is_active' => true,
            ],
            [
                'name' => 'Support Ticket Created',
                'type' => 'outgoing',
                'category' => 'Support',
                'description' => 'Email sent when a new support ticket is created',
                'subject_en' => 'Support Ticket Created - Ticket #{{ticket_id}}',
                'body_en' => '<p>Dear {{client_name}},</p><p>Your support ticket has been created successfully.</p><p><strong>Ticket Details:</strong></p><p>Ticket ID: {{ticket_id}}<br>Subject: {{ticket_subject}}<br>Status: {{ticket_status}}<br>Priority: {{ticket_priority}}</p><p>Our support team will respond to your ticket as soon as possible.</p><p>Best regards,<br>{{company_name}} Support Team</p>',
                'subject_ar' => 'تم إنشاء تذكرة دعم - تذكرة رقم {{ticket_id}}',
                'body_ar' => '<p>عزيزي {{client_name}}،</p><p>تم إنشاء تذكرة الدعم الخاصة بك بنجاح.</p><p><strong>تفاصيل التذكرة:</strong></p><p>رقم التذكرة: {{ticket_id}}<br>الموضوع: {{ticket_subject}}<br>الحالة: {{ticket_status}}<br>الأولوية: {{ticket_priority}}</p><p>سيقوم فريق الدعم لدينا بالرد على تذكرتك في أقرب وقت ممكن.</p><p>مع أطيب التحيات،<br>فريق دعم {{company_name}}</p>',
                'subject_fr' => 'Ticket de support créé - Ticket #{{ticket_id}}',
                'body_fr' => '<p>Cher {{client_name}},</p><p>Votre ticket de support a été créé avec succès.</p><p><strong>Détails du ticket:</strong></p><p>ID du ticket: {{ticket_id}}<br>Sujet: {{ticket_subject}}<br>Statut: {{ticket_status}}<br>Priorité: {{ticket_priority}}</p><p>Notre équipe de support répondra à votre ticket dès que possible.</p><p>Cordialement,<br>L\'équipe de support {{company_name}}</p>',
                'variables' => ['client_name', 'company_name', 'ticket_id', 'ticket_subject', 'ticket_status', 'ticket_priority'],
                'is_active' => true,
            ],
            [
                'name' => 'Service Activated',
                'type' => 'outgoing',
                'category' => 'Services',
                'description' => 'Email sent when a service is activated',
                'subject_en' => 'Service Activated - {{service_name}}',
                'body_en' => '<p>Dear {{client_name}},</p><p>Your service has been activated successfully!</p><p><strong>Service Details:</strong></p><p>Service: {{service_name}}<br>Domain: {{service_domain}}<br>Activation Date: {{activation_date}}</p><p>You can now access your service from your client area.</p><p>Best regards,<br>{{company_name}} Team</p>',
                'subject_ar' => 'تم تفعيل الخدمة - {{service_name}}',
                'body_ar' => '<p>عزيزي {{client_name}}،</p><p>تم تفعيل خدمتك بنجاح!</p><p><strong>تفاصيل الخدمة:</strong></p><p>الخدمة: {{service_name}}<br>النطاق: {{service_domain}}<br>تاريخ التفعيل: {{activation_date}}</p><p>يمكنك الآن الوصول إلى خدمتك من منطقة العملاء.</p><p>مع أطيب التحيات،<br>فريق {{company_name}}</p>',
                'subject_fr' => 'Service activé - {{service_name}}',
                'body_fr' => '<p>Cher {{client_name}},</p><p>Votre service a été activé avec succès!</p><p><strong>Détails du service:</strong></p><p>Service: {{service_name}}<br>Domaine: {{service_domain}}<br>Date d\'activation: {{activation_date}}</p><p>Vous pouvez maintenant accéder à votre service depuis votre espace client.</p><p>Cordialement,<br>L\'équipe {{company_name}}</p>',
                'variables' => ['client_name', 'company_name', 'service_name', 'service_domain', 'activation_date'],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}
