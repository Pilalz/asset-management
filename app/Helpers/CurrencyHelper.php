<?php

use App\Models\Company;

if (!function_exists('format_currency')) {
    /**
     * Memformat angka menjadi mata uang berdasarkan perusahaan yang aktif.
     *
     * @param float|int $amount
     * @return string
     */
    function format_currency($amount)
    {
        // Ambil ID perusahaan yang aktif dari sesi
        $activeCompanyId = session('active_company_id');

        if (!$activeCompanyId) {
            // Default ke IDR jika tidak ada perusahaan aktif
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }

        // Ambil data perusahaan (gunakan cache agar lebih cepat)
        $company = Cache::remember('company_' . $activeCompanyId, 3600, function () use ($activeCompanyId) {
            return Company::find($activeCompanyId);
        });

        $currencyCode = $company ? $company->currency : 'IDR';

        // Gunakan kelas NumberFormatter bawaan PHP untuk format yang benar
        $formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);

        if ($currencyCode === 'IDR') {
            $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);
        }
        elseif ($currencyCode === 'USD') {
            $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);
        }
        
        return $formatter->formatCurrency($amount, $currencyCode);
    }
}