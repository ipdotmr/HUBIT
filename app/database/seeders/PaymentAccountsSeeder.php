<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentAccount;

class PaymentAccountsSeeder extends Seeder
{
    public function run(): void
    {
        PaymentAccount::updateOrCreate(
            ['name' => 'Banque Centrale de Mauritanie - MRU'],
            [
                'type' => 'bank',
                'currency' => 'MRU',
                'account_number' => 'MR13 0000 0000 0000 0000 0000 000',
                'bank_name' => 'Banque Centrale de Mauritanie',
                'swift_bic' => 'BCMMMRTN',
                'active' => true,
                'instructions' => [
                    'en' => 'Transfer to account MR13 0000 0000 0000 0000 0000 000. Reference: Invoice number',
                    'ar' => 'التحويل إلى حساب MR13 0000 0000 0000 0000 0000 000. المرجع: رقم الفاتورة',
                    'fr' => 'Transférer sur le compte MR13 0000 0000 0000 0000 0000 000. Référence: Numéro de facture'
                ]
            ]
        );

        PaymentAccount::updateOrCreate(
            ['name' => 'Cash Payment - Nouakchott Office'],
            [
                'type' => 'cash',
                'currency' => 'MRU',
                'active' => true,
                'instructions' => [
                    'en' => 'Visit our office at Nouakchott, Boulevard de la République. Business hours: 8AM-5PM',
                    'ar' => 'قم بزيارة مكتبنا في نواكشوط، شارع الجمهورية. ساعات العمل: 8 صباحاً - 5 مساءً',
                    'fr' => 'Visitez notre bureau à Nouakchott, Boulevard de la République. Heures d\'ouverture: 8h-17h'
                ]
            ]
        );
    }
}
