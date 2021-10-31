<?php
namespace Productreview\Reviews\Block\System\Config;
 
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Productreview\Reviews\Helper\PhpTemplateEngine;
use Magento\Backend\Block\Template\Context;
use Productreview\Reviews\Helper\Repository;
use Productreview\Reviews\Helper\UrlGenerator;
use Productreview\Reviews\Model\ModuleDetails;

class ExportNativeReviews extends Field
{
    private $urlGenerator;
    private $repository;

    public function __construct(
        UrlGenerator $urlGenerator,
        Repository $repository,
        Context $context,
        array $data = []
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->repository   = $repository;

        parent::__construct($context, $data);
    }

    function render(AbstractElement $element)
    {
        $integrationState = $this->repository->getIntegrationState();

        if ($integrationState === null || $integrationState['status'] !== ModuleDetails::CONNECTION_STATUS_SUCCESS) {
            return PhpTemplateEngine::render(function () {
?>
<div>
    <?= __('Set up credentials first.') ?>
</div>
<?php
            });
        }

        return PhpTemplateEngine::render(function () {
            $url = $this->urlGenerator->generate(
                UrlGenerator::PR_APP_BM_EXTERNAL_CATALOG_CONTACT,
                [
                    'reason' => 'review_import'
                ]
            );

?>
<p style="padding: 1em; color: darkblue; background-color: lightblue; border: 1px solid darkblue; width: 700px">
    <?= strtr(
        __('In order to import reviews from other review platforms (Trustpilot, Yotpo, etc...) <contact_productreview_link>Contact ProductReview</contact_productreview_link>.'),
        [
            '<contact_productreview_link>'  => sprintf('<a href="%s">', $url),
            '</contact_productreview_link>' => '</a>'
        ]
    ) ?>
</p>
<p>
    <?= strtr(
        __('Export WooCommerce native reviews using the button below and <contact_productreview_link>Request a review import from ProductReview</contact_productreview_link>.'),
        [
            '<contact_productreview_link>'  => sprintf('<a href="%s">', $url),
            '</contact_productreview_link>' => '</a>'
        ]
    ) ?>
</p>

<button><?= __('Download Magento2 Native Reviews') ?></button>
<?php
        });
    }
}