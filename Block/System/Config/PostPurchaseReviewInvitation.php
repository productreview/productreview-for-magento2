<?php
namespace Productreview\Reviews\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Productreview\Reviews\Helper\PhpTemplateEngine;
use Productreview\Reviews\Helper\Repository;
use Productreview\Reviews\Helper\UrlGenerator;
use Productreview\Reviews\Model\ModuleDetails;

class PostPurchaseReviewInvitation extends Field
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
        $integrationState = $this->repository->getIntegrationState($this->getRequest()->getParam('store'));

        if ($integrationState === null || $integrationState['status'] !== ModuleDetails::CONNECTION_STATUS_SUCCESS) {
            return PhpTemplateEngine::render(function () {
?>
<div>
    <?= __('Set up credentials first.') ?>
</div>
<?php
            });
        }

        return PhpTemplateEngine::render(function () use ($integrationState) {
?>
<p>
    <i>
        <?= __('Whenever the status of an order changes to <strong>complete</strong> or <strong>canceled</strong> on Magento2, ProductReview gets notified automatically.') ?>
    </i>
</p>

<p style="padding: 1em; color: darkred; background-color: pink; border: 1px solid darkred; width: 700px">
    <?= strtr(
        __('You must <setup_campaign_link>Setup an Automatic Invitation Campaign</setup_campaign_link> to send post purchase review invitation.'),
        [
            '<setup_campaign_link>'  => sprintf('<a href="%s">', $this->urlGenerator->generate(
                UrlGenerator::PR_APP_BM_EXTERNAL_CATALOG_AUTOMATIC_CAMPAIGN_INVITATION,
                [
                    'brandSlug'           => $integrationState['brand']['slug'],
                    'externalCatalogSlug' => $integrationState['externalCatalog']['slug']
                ]
            )),
            '</setup_campaign_link>' =>'</a>'
        ]
    ) ?>
</p>
<?php
        });
    }
}
