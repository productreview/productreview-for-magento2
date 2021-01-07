<?php
namespace Productreview\Reviews\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Productreview\Reviews\Helper\PhpTemplateEngine;
use Productreview\Reviews\Helper\Repository;
use Productreview\Reviews\Helper\UrlGenerator;
use Productreview\Reviews\Model\ModuleDetails;

final class ShareAllPastOrders extends Field
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
?>
<p>
    <?= strtr(
        __('<important>Once you\'ve setup an Automatic Invitation Campaign</important>, use the form below to send review invitations to your existing customers.'),
        [
            '<important>'  => '<strong style="color: darkred">',
            '</important>' => '</strong>',
        ]
    ) ?>
</p>

<p>
    <i>
        <?= __('Only the latest order of each customer will be used to avoid sending multiple invitations to the same customer.<br />Depending on the amount of orders, this action can take up to few minutes.') ?>
    </i>
</p>

<div style="padding: 10px">RADIO BUTTONS</div>

<button><?= __('Invite your previous customers — Share all past orders with ProductReview') ?></button>
<?php
        });
    }
}
