<?php

/**
 * @project:   Event Based Mail Scheduler
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

/** @var array $campaigns */
/** @var string $selectedCampaign */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
?>

<form action="#" method="post">
    <?php echo $token->output("update_settings"); ?>

    <div class="form-group">
        <?php echo $form->label("selectedCampaign", t('Master Campaign')); ?>
        <?php echo $form->select("selectedCampaign", $campaigns, $selectedCampaign); ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions ">
            <button type="submit" class="btn btn-primary float-end">
                <i class="fas fa-save"></i> <?php echo t("Save"); ?>
            </button>
        </div>
    </div>
</form>