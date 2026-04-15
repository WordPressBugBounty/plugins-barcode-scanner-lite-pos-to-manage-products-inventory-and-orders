<?php


namespace Stripe;

class File extends ApiResource
{
    const OBJECT_NAME = 'file';

    const PURPOSE_ACCOUNT_REQUIREMENT = 'account_requirement';
    const PURPOSE_ADDITIONAL_VERIFICATION = 'additional_verification';
    const PURPOSE_BUSINESS_ICON = 'business_icon';
    const PURPOSE_BUSINESS_LOGO = 'business_logo';
    const PURPOSE_CUSTOMER_SIGNATURE = 'customer_signature';
    const PURPOSE_DISPUTE_EVIDENCE = 'dispute_evidence';
    const PURPOSE_DOCUMENT_PROVIDER_IDENTITY_DOCUMENT = 'document_provider_identity_document';
    const PURPOSE_FINANCE_REPORT_RUN = 'finance_report_run';
    const PURPOSE_FINANCIAL_ACCOUNT_STATEMENT = 'financial_account_statement';
    const PURPOSE_IDENTITY_DOCUMENT = 'identity_document';
    const PURPOSE_IDENTITY_DOCUMENT_DOWNLOADABLE = 'identity_document_downloadable';
    const PURPOSE_ISSUING_REGULATORY_REPORTING = 'issuing_regulatory_reporting';
    const PURPOSE_PCI_DOCUMENT = 'pci_document';
    const PURPOSE_PLATFORM_TERMS_OF_SERVICE = 'platform_terms_of_service';
    const PURPOSE_SELFIE = 'selfie';
    const PURPOSE_SIGMA_SCHEDULED_QUERY = 'sigma_scheduled_query';
    const PURPOSE_TAX_DOCUMENT_USER_UPLOAD = 'tax_document_user_upload';
    const PURPOSE_TERMINAL_ANDROID_APK = 'terminal_android_apk';
    const PURPOSE_TERMINAL_READER_SPLASHSCREEN = 'terminal_reader_splashscreen';

    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, Collection::class, $params, $opts);
    }

    public static function retrieve($id, $opts = null)
    {
        $opts = Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }

    const OBJECT_NAME_ALT = 'file_upload';

    use ApiOperations\Create {
        create as protected _create;
    }

    public static function create($params = null, $opts = null)
    {
        $opts = Util\RequestOptions::parse($opts);
        if (null === $opts->apiBase) {
            $opts->apiBase = Stripe::$apiUploadBase;
        }
        $flatParams = \array_column(Util\Util::flattenParams($params), 1, 0);

        return static::_create($flatParams, $opts);
    }
}
