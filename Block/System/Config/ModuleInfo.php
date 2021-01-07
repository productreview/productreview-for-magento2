<?php
namespace Productreview\Reviews\Block\System\Config;
 
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Productreview\Reviews\Helper\PhpTemplateEngine;
use Productreview\Reviews\Helper\Repository;
use Productreview\Reviews\Helper\UrlGenerator;
use Productreview\Reviews\Model\ModuleDetails;
use \Exception;

final class ModuleInfo extends Field
{
    private $scopeConfig;
    private $repository;
    private $urlGenerator;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Repository $repository,
        UrlGenerator $urlGenerator,
        Context $context,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;

        $this->repository   = $repository;
        $this->urlGenerator = $urlGenerator;

        parent::__construct($context, $data);
    }

    function render(AbstractElement $element)
    {
        if ($this->getRequest()->getParam('store') === null) {

            return strtr(
                __('Select specific store view using the <trigger>Store View Switcher</trigger> in order to setup the ProductReview module.'),
                [
                    '<trigger>'  => <<<HTML
<span
    style="color: blue; text-decoration: underline; cursor: pointer"
    onclick="window.setTimeout(function () { window.document.querySelectorAll('.store-switcher .admin__action-dropdown').forEach(function (e) { e.click(); }); })"
>
HTML
                    ,
                    '</trigger>' => '</span>'
                ]
            );
        }

        return PhpTemplateEngine::render(function () {
?>
<div>
    <div>
        <?php echo __('ProductReview Module Version: ') ?><strong><?php echo ModuleDetails::MODULE_VERSION; ?></strong>
        <?php echo strtr(
            __('(<github_link>See project on Github</github_link> — for developers)'),
            [
                '<github_link>' => sprintf('<a href="%s">', $this->urlGenerator->generate(UrlGenerator::EXTERNAL_GITHUB_REPOSITORY)),
                '</github_link>' => '</a>',
            ]
        ); ?>
    </div>

    <div>
        <?php echo $this->renderIntegrationState(); ?>
    </div>
</div>
<?php
        });
    }

    function renderIntegrationState()
    {
        return PhpTemplateEngine::render(function () {
            $integrationState = $this->repository->getIntegrationState();

            echo __('Status:') . ' ';

            switch ($integrationState['status']) {
                case ModuleDetails::CONNECTION_STATUS_SUCCESS:
                    echo '<strong style="color: green">' . __('Success — The connection with ProductReview was successfully established.') . '</strong>';
                    break;
                case ModuleDetails::CONNECTION_STATUS_FAIL:
                    echo '<strong style="color: darkred">' . __('Fail — An unexpected error occurred while trying to connect with ProductReview. Make sure the credentials are correct. Please, try again later. Contact support if the error persists.') . '</strong>';
                    break;
                case ModuleDetails::CONNECTION_STATUS_INVALID:
                    echo '<strong style="color: darkred">' . __('Invalid credentials — Make sure the values are correct. Contact support if the error persists.') . '</strong>';
                    break;
                case ModuleDetails::CONNECTION_STATUS_NO_CREDENTIALS:
                    echo '<strong style="color: orange">';
                    echo strtr(
                        __('Credentials Missing — In order to get your credentials, <install_link>Follow these instructions</install_link>, then copy and paste the credentials provided below.'),
                        [
                            '<install_link>' => sprintf('<a href="%s">', $this->urlGenerator->generate(UrlGenerator::PR_APP_BM_EXTERNAL_CATALOG_INTEGRATION_MAGENTO2_INSTALL)),
                            '</install_link>' => '</a>',
                        ]
                    );
                    echo '</strong>';
                    break;
                default:
                    throw new Exception('Invalid integration status.');
            }
        });
    }
}