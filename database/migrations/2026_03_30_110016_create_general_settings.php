<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'grdzelo.com');
        $this->migrator->add('general.brand_name', 'Grdzelo');
        $this->migrator->add('general.tagline', 'Product-Minded Software Engineer');
        $this->migrator->add('general.email', 'hello@grdzelo.com');
        $this->migrator->add('general.phone', '');
        $this->migrator->add('general.location', '');
        $this->migrator->add('general.footer_text', '');
        $this->migrator->add('general.copyright_text', '© ' . date('Y') . ' grdzelo.com. All rights reserved.');
        $this->migrator->add('general.analytics_id', '');
        $this->migrator->add('general.verification_tags', '');
        $this->migrator->add('general.default_cta_text', "Let's Work Together");
        $this->migrator->add('general.default_cta_url', '/contact');
        $this->migrator->add('general.contact_email', 'hello@grdzelo.com');
        $this->migrator->add('general.contact_form_enabled', true);
        $this->migrator->add('general.budget_ranges', [
            'Under $5,000',
            '$5,000 – $10,000',
            '$10,000 – $25,000',
            '$25,000 – $50,000',
            '$50,000+',
        ]);
    }
};
