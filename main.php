<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\ArchitectuiAsset;
use app\widgets\Alert;
use yii\bootstrap\Nav;
use app\models\Employee;
use app\models\CargoBarcode;

ArchitectuiAsset::register($this);

function my_mb_ucfirst($str) {
    $fc = mb_strtoupper(mb_substr($str, 0, 1));
    return $fc.mb_substr($str, 1);
}

$identity = \Yii::$app->user->identity;
$isAdeliya = false;
if ($identity->username == 'Nargiza' || $identity->username == 'Ruslan') {
    $isAdeliya = true;
}

$loginName  = '';
$userName   = '';
$branchName = '';
$regionName = '';
if ($identity->username) {
    $loginName = $identity->username;
}
if ($identity->fio) {
    $userName = $identity->fio;
}
if ($identity->branch) {
    $branchName = $identity->branch->name;
    if ($identity->branch->region) {
        $regionName = $identity->branch->region->name;
    }
}
$img_src = "";
    // vd($identity->id);
$employee = Employee::findOne(['user_id'=>$identity->id]);
if($employee){
    $img_src = $employee->qrcode_key;
    $img_src = str_replace("http://employee.bts.uz/a?key=", "", $img_src);
}   
$currentRoute = Yii::$app->request->getPathInfo();

$identity = \Yii::$app->user->identity;
$userBranchId = $identity->branchId;

$soundsBarcode = 0;
$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;
$urlAddress = $controller . '/' . $action;

if ($urlAddress == "monitoring/home")
{
    $barcodes = CargoBarcode::find()
        ->where([
            'OR',
            ['from_branch_id' => $userBranchId],
            ['branch_id' => $userBranchId],
        ])
        // Топилмаган почталар
        ->andWhere(['status' => 3])
        ->all();

    if (!empty($barcodes) and ($userBranchId == $barcodes[0]->from_branch_id or $userBranchId == $barcodes[0]->branch_id) and in_array($identity->roleId, [1,2,5]))
    {
        $countBarcodes = count($barcodes);
        $soundsBarcode = 2;
    }
    elseif(in_array($identity->roleId, [8,99]))
    {
        $barcodes = CargoBarcode::find()
        // Топилмаган почталар
        ->andWhere(['status' => 3])
        ->all();
        $countBarcodes = count($barcodes);

        if ($countBarcodes > 0)
        {
            $soundsBarcode = 2;
        }
    }
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="<?= Yii::$app->language ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>">
    <title><?= Yii::$app->controller->title; ?></title>
    <meta name="viewport"
    content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <!-- Disable tap highlight on IE -->
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="icon" type="image/ico" href="/favicon.ico" />
    <?php $this->head() ?>
    <link rel="stylesheet" href="/css/jquerysctipttop.css">
    <style type="text/css">
        html {
            font-size: 16px;
            font-weight: 400;
        }

        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        label {
            font-weight: 500;
        }

        .btn {
            white-space: nowrap;
        }

        .hidden {
            display: none !important;
        }

        .hide {
            display: none !important;
        }

        .app-page-title {
            padding: 15px 30px;
        }

        .app-main__outer,
        .app-main__inner {
            max-width: 100%;
        }

        .table-responsive {
            max-width: 100%;
            min-width: 1%;
            overflow-x: auto;
        }

        .required label.control-label:after {
            content: " *";
            color: red;
        }

        .closed-sidebar.fixed-footer .app-footer__inner {
            margin-left: 80px !important;
        }

        .btn-default {
            color: #212529;
            background-color: #eeeeee;
            border-color: gainsboro;
        }

        .btn-default:hover {
            color: #212529;
            background-color: #dbdbdb;
            border-color: #d5d5d5;
        }

        .btn-group-xs>.btn,
        .btn-xs {
            line-height: 1;
        }

        .vertical-nav-menu.metismenu li a {
            text-transform: lowercase;
        }

        .vertical-nav-menu.metismenu li a:first-letter {
            text-transform: uppercase;
        }

        .swal2-container.swal2-center .swal2-header .swal2-title img {
            border: 3px solid #e77713;
            width: 80px;
            border-radius: 50%;
            position: absolute;
            background-color: #fff;
            top: -55px;
            left: -35px;
        }

        .swal2-icon.swal2-warning.swal2-animate-warning-icon {
            display: none !important;
        }

        h4,
        .h4 {
            font-size: 16px;
        }

        body>.modal-backdrop,
        .blockOverlay {
            display: none;
        }

        .rc-handle-container .rc-handle {
            display: none;
        }

        .kv-drp-dropdown .right-ind.kv-clear {
            display: none;
        }

        .daterangepicker .ranges .range_inputs>div {
            /*float: none;*/
        }

        /* Switchery defaults. */
        .switchery {
            background-color: #fff;
            border: 1px solid #dfdfdf;
            border-radius: 20px;
            cursor: pointer;
            display: inline-block;
            height: 30px;
            position: relative;
            vertical-align: middle;
            width: 50px;
            -moz-user-select: none;
            -khtml-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
            box-sizing: content-box;
            background-clip: content-box;
        }

        .switchery>small {
            background: #fff;
            border-radius: 100%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
            height: 30px;
            position: absolute;
            top: 0;
            width: 30px;
        }

        /* Switchery sizes. */
        .switchery-small {
            border-radius: 20px;
            height: 20px;
            width: 33px;
        }

        .switchery-small>small {
            height: 20px;
            width: 20px;
        }

        .switchery-large {
            border-radius: 40px;
            height: 40px;
            width: 66px;
        }

        .switchery-large>small {
            height: 40px;
            width: 40px;
        }

        /*.fixed-sidebar .app-header .app-header__logo
                {
                    display: none;
                }
                .fixed-sidebar.closed-sidebar .app-header
                {
                    left-left: 80px;
                }
                .fixed-sidebar.fixed-header .app-sidebar .app-header__logo
                {
                    display: flex;
                }
                .fixed-sidebar.closed-sidebar .app-sidebar .app-header__logo
                {
                    width: 80px;
                    padding: 0;
                }*/
                .app-header__logo .logo-src {
                    background: none;
                }

                .container-site-update {
                    width: 1160px;
                    margin: 0 auto;
                }

                .multiple-fields .control-label {
                    display: none;
                }

                .form-group {
                    position: relative;
                    min-height: 38px;
                    padding-top: 38px;
                    margin-bottom: 15px !important;
                    background: #FFFFFF;
                    border-radius: 0.25rem;
                }

                .form-group .form-control {
                    position: absolute;
                    top: 0;
                    left: 0;
                    background: transparent;
                    color: transparent;
                }

                .form-control:disabled,
                .form-control[readonly] {
                    background-color: rgba(0, 0, 0, 0.1);
                }

                .form-group .control-label {
                    position: absolute;
                    top: 1px;
                    left: 10px;
                    width: calc(100% - 11px);
                    margin: 0;
                    padding: 7px 20px;
                    cursor: text;
                    border-radius: 5px;
                    background: #FFFFFF;
                    transition: all 200ms ease-in;
                }

                .form-group .form-control:hover {
                    border-color: #A9BCEE;
                    outline: none;
                    box-shadow: none;
                }

                .form-group .form-control:focus {
                    background: none;
                    box-shadow: none;
                    border-color: #3F6AD8;
                    color: #495057;
                }

                .form-group .form-control:disabled:focus,
                .form-control[readonly]:focus {
                    background-color: rgba(0, 0, 0, 0.1);
                }

                .form-group .control-label-top {
                    top: -9px !important;
                    font-size: 80% !important;
                    padding: 0 5px !important;
                    background: inherit !important;
                    left: 10px !important;
                    /*border: 1px solid #3F6AD8 !important;*/
                    width: auto !important;
                    z-index: 1000 !important;
                }

                .form-control-active {
                    color: #495057 !important;
                }

                .form-control-active+.select2-container--default .select2-selection--single .select2-selection__rendered {
                    color: #495057 !important;
                }

                .form-group .select2-container--default .select2-selection--single .select2-selection__rendered {
                    /*color: transparent;*/
                }

                .input-group .input-group-prepend+.input-group-prepend {
                    flex: none;
                    width: auto;
                }

                .select2-container {
                    width: 100% !important;
                }

                .form-group .select2-container .select2-selection--single {
                    height: 38px;
                    padding: 4px;
                    border: 1px solid #ced4da;
                    background: transparent;
                }

                .select2-container--default .select2-selection--single .select2-selection__arrow {
                    top: 5px;
                }

                .padding-top-0 {
                    padding-top: 0 !important;
                    background: transparent;
                }

                .has-error .form-control {
                    border-color: #a94442;
                    -webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
                    box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
                }

                .help-block {
                    display: block;
                    margin-top: 5px;
                    margin-bottom: 10px;
                    color: #737373;
                }

                .has-error .help-block,
                .has-error .control-label,
                .has-error .radio,
                .has-error .checkbox,
                .has-error .radio-inline,
                .has-error .checkbox-inline,
                .has-error.radio label,
                .has-error.checkbox label,
                .has-error.radio-inline label,
                .has-error.checkbox-inline label {
                    color: #a94442;
                }

                .has-success .form-control {
                    border-color: #3c763d;
                    -webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
                    box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
                }

                .has-success .help-block,
                .has-success .control-label,
                .has-success .radio,
                .has-success .checkbox,
                .has-success .radio-inline,
                .has-success .checkbox-inline,
                .has-success.radio label,
                .has-success.checkbox label,
                .has-success.radio-inline label,
                .has-success.checkbox-inline label {
                    color: #3c763d;
                }

                .help-block {
                    margin: 0;
                }

                .btn-group,
                .btn-group-vertical {
                    flex-wrap: wrap;
                    justify-content: center;
                }

                .form-group .input-group.date .input-group-addon i {
                    vertical-align: -webkit-baseline-middle;
                }

                .form-group .input-group {
                    position: absolute;
                    top: 0;
                    left: 0;
                    height: 38px;
                }

                .form-group .input-group>.form-control {
                    width: 100%;
                    padding-left: calc(80px + 0.75rem);
                }

                .form-group .input-group>.input-group-addon {
                    height: 100%;
                }

                .form-check {
                    position: relative;
                    display: block;
                    padding-left: 0rem;
                }

                .form-check-input {
                    position: absolute;
                    margin-top: 0.3rem;
                    margin-left: -1.25rem;
                }

                .form-check-input[disabled]~.form-check-label,
                .form-check-input:disabled~.form-check-label {
                    color: #6c757d;
                }

                .form-check-inline .form-check-input {
                    position: static;
                    margin-top: 0;
                    margin-right: 0.3125rem;
                    margin-left: 0;
                }

                .was-validated .form-check-input:valid~.form-check-label,
                .form-check-input.is-valid~.form-check-label {
                    color: #28a745;
                }

                .was-validated .form-check-input:valid~.valid-feedback,
                .was-validated .form-check-input:valid~.valid-tooltip,
                .form-check-input.is-valid~.valid-feedback,
                .form-check-input.is-valid~.valid-tooltip {
                    display: block;
                }

                .was-validated .form-check-input:invalid~.form-check-label,
                .form-check-input.is-invalid~.form-check-label {
                    color: #dc3545;
                }

                .was-validated .form-check-input:invalid~.invalid-feedback,
                .was-validated .form-check-input:invalid~.invalid-tooltip,
                .form-check-input.is-invalid~.invalid-feedback,
                .form-check-input.is-invalid~.invalid-tooltip {
                    display: block;
                }

                .form-check-input:not(:checked),
                .form-check-input:checked {
                    position: absolute;
                    pointer-events: none;
                    opacity: 0
                }

                .kv-thead-float {
                    background: #FFFFFF;
                    /*width: calc(100% - 180px) !important;*/
                    /*overflow-x: hidden;*/
                }

                .kv-grid-table thead tr th {
                    text-align: center;
                }

                .modal-header {
                    /*flex-direction: row-reverse;*/
                    display: block;
                    position: relative;
                }

                .modal-header h3 {
                    padding-top: 30px;
                    text-align: center;
                }

                .modal-logo {
                    position: absolute;
                    left: 50%;
                    top: 0;
                    transform: translate(-50%, -50%);
                    width: 80px;
                    height: 80px;
                    padding-top: 17px;
                    border: 3px solid #e77713;
                    border-radius: 50%;
                    background: #FFFFFF;
                }

                .modal-logo img {
                    max-width: 100%;
                }

                .select2-hidden-accessible {
                    display: none;
                }

                .pagination li span {
                    position: relative;
                    display: block;
                    padding: 0.5rem 0.75rem;
                    margin-left: -1px;
                    line-height: 1.25;
                    color: #007bff;
                    background-color: #fff;
                    border: 1px solid #dee2e6;
                }

                .form-check-input[type="radio"]:not(:checked)+label,
                .form-check-input[type="radio"]:checked+label,
                label.btn input[type="radio"]:not(:checked)+label,
                label.btn input[type="radio"]:checked+label {
                    position: relative;
                    display: inline-block;
                    height: 1.5625rem;
                    padding-left: 35px;
                    line-height: 1.5625rem;
                    cursor: pointer;
                    -webkit-user-select: none;
                    -moz-user-select: none;
                    -ms-user-select: none;
                    user-select: none;
                    -webkit-transition: 0.28s ease;
                    transition: 0.28s ease
                }

                .form-check-input[type="radio"]+label:before,
                .form-check-input[type="radio"]+label:after,
                label.btn input[type="radio"]+label:before,
                label.btn input[type="radio"]+label:after {
                    position: absolute;
                    top: 0;
                    left: 0;
                    z-index: 0;
                    width: 16px;
                    height: 16px;
                    margin: 4px;
                    content: "";
                    -webkit-transition: 0.28s ease;
                    transition: 0.28s ease
                }

                .form-check-input[type="radio"]:not(:checked)+label:before,
                .form-check-input[type="radio"]:not(:checked)+label:after,
                .form-check-input[type="radio"]:checked+label:before,
                .form-check-input[type="radio"]:checked+label:after,
                .form-check-input[type="radio"].with-gap:checked+label:before,
                .form-check-input[type="radio"].with-gap:checked+label:after,
                label.btn input[type="radio"]:not(:checked)+label:before,
                label.btn input[type="radio"]:not(:checked)+label:after,
                label.btn input[type="radio"]:checked+label:before,
                label.btn input[type="radio"]:checked+label:after,
                label.btn input[type="radio"].with-gap:checked+label:before,
                label.btn input[type="radio"].with-gap:checked+label:after {
                    border-radius: 50%
                }

                .form-check-input[type="radio"]:not(:checked)+label:before,
                .form-check-input[type="radio"]:not(:checked)+label:after,
                label.btn input[type="radio"]:not(:checked)+label:before,
                label.btn input[type="radio"]:not(:checked)+label:after {
                    border: 2px solid #5a5a5a
                }

                .form-check-input[type="radio"]:not(:checked)+label:after,
                label.btn input[type="radio"]:not(:checked)+label:after {
                    -webkit-transform: scale(0);
                    transform: scale(0)
                }

                .form-check-input[type="radio"]:checked+label:before,
                label.btn input[type="radio"]:checked+label:before {
                    border: 2px solid transparent
                }

                .form-check-input[type="radio"]:checked+label:after,
                .form-check-input[type="radio"].with-gap:checked+label:before,
                .form-check-input[type="radio"].with-gap:checked+label:after,
                label.btn input[type="radio"]:checked+label:after,
                label.btn input[type="radio"].with-gap:checked+label:before,
                label.btn input[type="radio"].with-gap:checked+label:after {
                    border: 2px solid #4285f4
                }

                .form-check-input[type="radio"]:checked+label:after,
                .form-check-input[type="radio"].with-gap:checked+label:after,
                label.btn input[type="radio"]:checked+label:after,
                label.btn input[type="radio"].with-gap:checked+label:after {
                    background-color: #4285f4
                }

                .form-check-input[type="radio"]:checked+label:after,
                label.btn input[type="radio"]:checked+label:after {
                    -webkit-transform: scale(1.02);
                    transform: scale(1.02)
                }

                .form-check-input[type="radio"].with-gap:checked+label:after,
                label.btn input[type="radio"].with-gap:checked+label:after {
                    -webkit-transform: scale(0.5);
                    transform: scale(0.5)
                }

                .form-check-input[type="radio"].with-gap:disabled:checked+label:before,
                label.btn input[type="radio"].with-gap:disabled:checked+label:before {
                    border: 2px solid rgba(0, 0, 0, 0.46)
                }

                .form-check-input[type="radio"].with-gap:disabled:checked+label:after,
                label.btn input[type="radio"].with-gap:disabled:checked+label:after {
                    background-color: rgba(0, 0, 0, 0.46);
                    border: none
                }

                .form-check-input[type="radio"]:disabled:not(:checked)+label:before,
                .form-check-input[type="radio"]:disabled:checked+label:before,
                label.btn input[type="radio"]:disabled:not(:checked)+label:before,
                label.btn input[type="radio"]:disabled:checked+label:before {
                    background-color: transparent;
                    border-color: rgba(0, 0, 0, 0.46)
                }

                .form-check-input[type="radio"]:disabled+span,
                label.btn input[type="radio"]:disabled+span {
                    color: rgba(0, 0, 0, 0.46)
                }

                .form-check-input[type="radio"]:disabled:not(:checked)+span:before,
                label.btn input[type="radio"]:disabled:not(:checked)+span:before {
                    border-color: rgba(0, 0, 0, 0.46)
                }

                .form-check-input[type="radio"]:disabled:checked+span:after,
                label.btn input[type="radio"]:disabled:checked+span:after {
                    background-color: rgba(0, 0, 0, 0.46);
                    border-color: #bdbdbd
                }

                .form-check-input[type="radio"]:checked+label:after .disabled-material,
                label.btn input[type="radio"]:checked+label:after .disabled-material {
                    background-color: rgba(66, 133, 244, 0.2)
                }

                .form-check-input[type="radio"]:focus+label:before,
                label.btn input[type="radio"]:focus+label:before {
                    border-color: #80bdff;
                    -webkit-box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
                    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25)
                }

                .form-check-input[type="checkbox"]+label,
                label.btn input[type="checkbox"]+label {
                    position: relative;
                    display: inline-block;
                    height: 1.5625rem;
                    padding-left: 25px;
                    line-height: 1.5625rem;
                    cursor: pointer;
                    -webkit-user-select: none;
                    -moz-user-select: none;
                    -ms-user-select: none;
                    user-select: none
                }

                .form-check-input[type="checkbox"]+label.form-check-label-left,
                label.btn input[type="checkbox"]+label.form-check-label-left {
                    padding: 0 35px 0 0 !important
                }

                .form-check-input[type="checkbox"]+label.form-check-label-left:before,
                label.btn input[type="checkbox"]+label.form-check-label-left:before {
                    right: 0;
                    left: 100% !important;
                    -webkit-transform: translateX(-100%);
                    transform: translateX(-100%)
                }

                .form-check-input[type="checkbox"]+label:before,
                .form-check-input[type="checkbox"]:not(.filled-in)+label:after,
                label.btn input[type="checkbox"]+label:before,
                label.btn input[type="checkbox"]:not(.filled-in)+label:after {
                    position: absolute;
                    top: 0;
                    left: 0;
                    z-index: 0;
                    width: 18px;
                    height: 18px;
                    margin-top: 3px;
                    content: "";
                    border: 2px solid #495057;
                    border-radius: 1px;
                    -webkit-transition: .2s;
                    transition: .2s
                }

                .form-check-input[type="checkbox"]:not(.filled-in)+label:after,
                label.btn input[type="checkbox"]:not(.filled-in)+label:after {
                    border: 0;
                    -webkit-transform: scale(0);
                    transform: scale(0)
                }

                .form-check-input[type="checkbox"]:not(:checked):disabled+label:before,
                label.btn input[type="checkbox"]:not(:checked):disabled+label:before {
                    background-color: #bdbdbd;
                    border: none
                }

                .form-check-input[type="checkbox"]:checked+label:before,
                label.btn input[type="checkbox"]:checked+label:before {
                    top: -4px;
                    left: -5px;
                    width: 12px;
                    height: 1.375rem;
                    border-top: 2px solid transparent;
                    border-right: 2px solid #4285f4;
                    border-bottom: 2px solid #4285f4;
                    border-left: 2px solid transparent;
                    -webkit-transform: rotate(40deg);
                    transform: rotate(40deg);
                    -webkit-transform-origin: 100% 100%;
                    transform-origin: 100% 100%;
                    -webkit-backface-visibility: hidden;
                    backface-visibility: hidden
                }

                .form-check-input[type="checkbox"]:checked+label.form-check-label-left:before,
                label.btn input[type="checkbox"]:checked+label.form-check-label-left:before {
                    -webkit-transform: translateX(0) rotateZ(40deg);
                    transform: translateX(0) rotateZ(40deg);
                    -webkit-transform-origin: 0 0;
                    transform-origin: 0 0
                }

                .form-check-input[type="checkbox"]:checked:disabled+label:before,
                label.btn input[type="checkbox"]:checked:disabled+label:before {
                    border-right: 2px solid #bdbdbd;
                    border-bottom: 2px solid #bdbdbd
                }

                .form-check-input[type="checkbox"]:indeterminate+label:before,
                label.btn input[type="checkbox"]:indeterminate+label:before {
                    top: -11px;
                    left: -12px;
                    width: 10px;
                    height: 1.375rem;
                    border-top: none;
                    border-right: 2px solid #4285f4;
                    border-bottom: none;
                    border-left: none;
                    -webkit-transform: rotate(90deg);
                    transform: rotate(90deg);
                    -webkit-transform-origin: 100% 100%;
                    transform-origin: 100% 100%;
                    -webkit-backface-visibility: hidden;
                    backface-visibility: hidden
                }

                .form-check-input[type="checkbox"]:indeterminate+label.form-check-label-left:before,
                label.btn input[type="checkbox"]:indeterminate+label.form-check-label-left:before {
                    top: 0;
                    -webkit-transform-origin: 0 0;
                    transform-origin: 0 0
                }

                .form-check-input[type="checkbox"]:indeterminate:disabled+label:before,
                label.btn input[type="checkbox"]:indeterminate:disabled+label:before {
                    background-color: transparent;
                    border-right: 2px solid rgba(0, 0, 0, 0.46)
                }

                .form-check-input[type="checkbox"].filled-in+label:after,
                label.btn input[type="checkbox"].filled-in+label:after {
                    border-radius: .125rem
                }

                .form-check-input[type="checkbox"].filled-in+label:before,
                .form-check-input[type="checkbox"].filled-in+label:after,
                label.btn input[type="checkbox"].filled-in+label:before,
                label.btn input[type="checkbox"].filled-in+label:after {
                    position: absolute;
                    left: 0;
                    z-index: 1;
                    content: "";
                    -webkit-transition: border 0.25s, background-color 0.25s, width 0.2s .1s, height 0.2s .1s, top 0.2s .1s, left 0.2s .1s;
                    transition: border 0.25s, background-color 0.25s, width 0.2s .1s, height 0.2s .1s, top 0.2s .1s, left 0.2s .1s
                }

                .form-check-input[type="checkbox"].filled-in:not(:checked)+label:before,
                label.btn input[type="checkbox"].filled-in:not(:checked)+label:before {
                    top: 10px;
                    left: 6px;
                    width: 0;
                    height: 0;
                    border: 3px solid transparent;
                    -webkit-transform: rotateZ(37deg);
                    transform: rotateZ(37deg);
                    -webkit-transform-origin: 100% 100%;
                    transform-origin: 100% 100%
                }

                .form-check-input[type="checkbox"].filled-in:not(:checked)+label:after,
                label.btn input[type="checkbox"].filled-in:not(:checked)+label:after {
                    top: 0;
                    z-index: 0;
                    width: 20px;
                    height: 20px;
                    background-color: transparent;
                    border: 2px solid #5a5a5a
                }

                .form-check-input[type="checkbox"].filled-in:checked+label:before,
                label.btn input[type="checkbox"].filled-in:checked+label:before {
                    top: 0;
                    left: 1px;
                    width: 8px;
                    height: 13px;
                    border-top: 2px solid transparent;
                    border-right: 2px solid #fff;
                    border-bottom: 2px solid #fff;
                    border-left: 2px solid transparent;
                    -webkit-transform: rotateZ(37deg);
                    transform: rotateZ(37deg);
                    -webkit-transform-origin: 100% 100%;
                    transform-origin: 100% 100%
                }

                .form-check-input[type="checkbox"].filled-in:checked+label:after,
                label.btn input[type="checkbox"].filled-in:checked+label:after {
                    top: 0;
                    z-index: 0;
                    width: 20px;
                    height: 20px;
                    background-color: #a6c;
                    border: 2px solid #a6c
                }

                .form-check-input[type="checkbox"].filled-in.filled-in-danger:checked+label:after,
                label.btn input[type="checkbox"].filled-in.filled-in-danger:checked+label:after {
                    background-color: #f44336;
                    border-color: #f44336
                }

                .form-check-input[type="checkbox"]:disabled:not(:checked)+label:before,
                label.btn input[type="checkbox"]:disabled:not(:checked)+label:before {
                    background-color: #bdbdbd;
                    border-color: #bdbdbd
                }

                .form-check-input[type="checkbox"]:disabled:not(:checked)+label:after,
                label.btn input[type="checkbox"]:disabled:not(:checked)+label:after {
                    background-color: #bdbdbd;
                    border-color: #bdbdbd
                }

                .form-check-input[type="checkbox"]:disabled:checked+label:before,
                label.btn input[type="checkbox"]:disabled:checked+label:before {
                    background-color: transparent
                }

                .form-check-input[type="checkbox"]:disabled:checked+label:after,
                label.btn input[type="checkbox"]:disabled:checked+label:after {
                    background-color: #bdbdbd;
                    border-color: #bdbdbd
                }

                .form-check-input[type="checkbox"]:focus:not(:checked)+label::before,
                label.btn input[type="checkbox"]:focus:not(:checked)+label::before {
                    border-color: #007bff;
                    -webkit-box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
                    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25)
                }

                .form-check-input[type="checkbox"]:focus:checked+label::before,
                label.btn input[type="checkbox"]:focus:checked+label::before {
                    border-color: #007bff;
                    -webkit-box-shadow: 3px 2px 0 0 rgba(0, 123, 255, 0.25);
                    box-shadow: 3px 2px 0 0 rgba(0, 123, 255, 0.25);
                    border-top: 2px solid transparent;
                    border-left: 2px solid transparent
                }

                .form-control::placeholder {
                    color: transparent;
                }

                .modal-lg {
                    width: calc(100% - 220px) !important;
                    max-width: calc(100% - 220px) !important;
                }

                .dropdown-menu {
                    /*min-width: auto;*/
                    box-shadow: 0 0.46875rem 2.1875rem rgb(4 9 20 / 3%),
                    0 0.9375rem 1.40625rem rgb(4 9 20 / 3%),
                    0 0.25rem 0.53125rem rgb(4 9 20 / 5%),
                    0 0.125rem 0.1875rem rgb(4 9 20 / 3%),
                    0 0 0 1px rgb(255 255 255 / 55%);
                }

                .daterangepicker .ranges {
                    padding: 0;
                }

                .range_inputs .btn {
                    font-weight: normal;
                }

                .daterangepicker .calendar td,
                .daterangepicker .calendar th {
                    min-width: 30px;
                }

                .fixed-footer .app-footer {
                    z-index: 100000;
                }

                @media (max-width: 1270px) {
                    .container-site-update {
                        width: 1120px;
                    }

                    .form-group .control-label {
                        padding: 7px 10px;
                    }
                }

                @media (max-width: 1199.98px) {
                    .container-site-update {
                        width: 100%;
                    }

                    .form-group {
                        min-height: auto;
                    }
                }

                @media (max-width: 991.98px) {
                    .logoBtsPC {
                        display: none;
                    }

                    .closed-sidebar.fixed-footer .app-footer__inner {
                        margin-left: 0 !important;
                    }
                }

                @media (min-width: 576px) {
                    .form-inline .form-check-input {
                        position: relative;
                        -ms-flex-negative: 0;
                        flex-shrink: 0;
                        margin-top: 0;
                        margin-right: 0.25rem;
                        margin-left: 0;
                    }

                    .modal-dialog {
                        margin: 120px auto;
                    }
                }

                @media (min-width: 564px) {
                    .daterangepicker {
                        /*width: 160px;*/
                    }

                    .daterangepicker .ranges ul {
                        width: auto;
                    }
                }

                @media (min-width: 730px) {
                    .daterangepicker .ranges {
                        width: 160px;
                    }
                }
            </style>
        </head>

        <body>
            <?php $this->beginBody() ?>
            <div class="app-container app-theme-white body-tabs-shadow closed-sidebar fixed-sidebar fixed-footer">
                <div class="app-header header-shadow bg-asteroid header-text-light">
                    <div class="app-header__logo">
                        <div class="logo-src" style="background: none; width: auto; height: 60px;">
                            <a href="<?= Yii::$app->homeUrl ?>"><img src="/img/logo_new.png" alt="BTS" height="60"></a>
                        </div>
                        <div class="header__pane ml-auto">
                            <div>
                                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                                data-class="closed-sidebar">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="app-header__mobile-menu">
                    <div>
                        <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="app-header__menu">
                    <span>
                        <button type="button"
                        class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>
            <div class="app-header__content">
                <div class="app-header-left">
                    <a href="<?= Yii::$app->homeUrl ?>"><img src="/img/logo_new.png" class="logoBtsPC" alt="BTS"
                        height="60" style="margin-right: 15px;"></a>
                        <ul class="header-megamenu nav">
                            <li class="btn-group nav-item">
                                <a class="nav-link" data-toggle="dropdown" aria-expanded="false">
                                    <span class="badge badge-pill badge-danger ml-0 mr-2">4</span>
                                    <?= Yii::t('app', 'Настройки')?>
                                    <i class="fa fa-angle-down ml-2 opacity-5"></i>
                                </a>
                                <div tabindex="-1" role="menu" aria-hidden="true" class="rm-pointers dropdown-menu">
                                    <div class="dropdown-menu-header">
                                        <div class="dropdown-menu-header-inner bg-secondary">
                                            <div class="menu-header-image opacity-5"
                                            style="background-image: url('/themes/architectui/images/dropdown-header/abstract2.jpg');">
                                        </div>
                                        <div class="menu-header-content">
                                            <h5 class="menu-header-title"><?= Yii::t('app','Обзор')?></h5>
                                            <h6 class="menu-header-subtitle"><?= Yii::t('app','Выпадающие меню для всех')?></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="scroll-area-xs">
                                    <div class="scrollbar-container">
                                        <h6 tabindex="-1" class="dropdown-header"><?= Yii::t('app', 'Ключевые цифры')?></h6>
                                        <button type="button" tabindex="0" class="dropdown-item"><?= Yii::t('app', 'Календарь обслуживания')?></button>
                                        <button type="button" tabindex="0" class="dropdown-item"><?= Yii::t('app', 'База знаний')?></button>
                                        <button type="button" tabindex="0" class="dropdown-item"><?= Yii::t('app','Счета')?></button>
                                        <div tabindex="-1" class="dropdown-divider"></div>
                                        <button type="button" tabindex="0" class="dropdown-item"><?= Yii::t('app', 'Продукты')?></button>
                                        <button type="button" tabindex="0" class="dropdown-item"><?= Yii::t('app', 'Сводные запросы ')?></button>
                                    </div>
                                </div>
                                <ul class="nav flex-column">
                                    <li class="nav-item-divider nav-item"></li>
                                    <li class="nav-item-btn nav-item">
                                        <button class="btn-wide btn-shadow btn btn-danger btn-sm"><?= Yii::t('app', 'Отмена')?></button>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="dropdown nav-item">
                            <a aria-haspopup="true" data-toggle="dropdown" class="nav-link" aria-expanded="false">
                                <i class="nav-link-icon pe-7s-settings"></i>
                                <?= Yii::t('app', 'Проекты')?>
                                <i class="fa fa-angle-down ml-2 opacity-5"></i>
                            </a>
                            <div tabindex="-1" role="menu" aria-hidden="true"
                            class="dropdown-menu-rounded dropdown-menu-lg rm-pointers dropdown-menu">
                            <div class="dropdown-menu-header">
                                <div class="dropdown-menu-header-inner bg-success">
                                    <div class="menu-header-image opacity-1"
                                    style="background-image: url('/themes/architectui/images/dropdown-header/abstract3.jpg');">
                                </div>
                                <div class="menu-header-content text-left">
                                    <h5 class="menu-header-title"><?= Yii::t('app','Обзор')?></h5>
                                    <h6 class="menu-header-subtitle"><?= Yii::t('app', 'Неограниченные возможности')?></h6>
                                    <div class="menu-header-btn-pane">
                                        <button class="mr-2 btn btn-dark btn-sm">
                                         <?= Yii::t('app', 'Настройки')?>
                                     </button>
                                     <button class="btn-icon btn-icon-only btn btn-warning btn-sm">
                                        <i class="pe-7s-config btn-icon-wrapper"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" tabindex="0" class="dropdown-item">
                        <i class="dropdown-icon lnr-file-empty"></i>
                        <?= Yii::t('app', 'Графический дизайн')?>
                    </button>
                    <button type="button" tabindex="0" class="dropdown-item">
                        <i class="dropdown-icon lnr-file-empty"></i>
                        <?= Yii::t('app', 'Разработка приложений')?> 
                    </button>
                    <button type="button" tabindex="0" class="dropdown-item">
                        <i class="dropdown-icon lnr-file-empty"></i>
                        <?= Yii::t('app', 'Дизайн иконок')?>
                    </button>
                    <div tabindex="-1" class="dropdown-divider"></div>
                    <button type="button" tabindex="0" class="dropdown-item">
                        <i class="dropdown-icon lnr-file-empty"></i>
                        <?= Yii::t('app', 'Разное')?>
                    </button>
                    <button type="button" tabindex="0" class="dropdown-item">
                        <i class="dropdown-icon lnr-file-empty"></i>
                        <?= Yii::t('app', 'Фронтенд-разработчик')?>
                    </button>
                </div>
            </li>
        </ul>
    </div>
    <div class="app-header-right">
        <div class="search-wrapper">
            <div class="input-holder">
                <form class="waybill-search-form" action="<?= Url::to(['site/search-fast']) ?>"
                    method="GET">
                    <input name="term" type="text" class="search-input" placeholder="Поиск ...">
                    <button class="search-icon">
                        <span></span>
                    </button>
                </form>
            </div>
            <button class="close"></button>
        </div>
        <div class="header-dots">
            <div class="dropdown">
                <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"
                class="p-0 mr-2 btn btn-link">
                <span class="icon-wrapper icon-wrapper-alt rounded-circle">
                    <span class="icon-wrapper-bg bg-primary"></span>
                    <i class="icon text-primary ion-android-apps"></i>
                </span>
            </button>
            <div tabindex="-1" role="menu" aria-hidden="true"
            class="dropdown-menu-xl rm-pointers dropdown-menu dropdown-menu-right">
            <div class="dropdown-menu-header">
                <div class="dropdown-menu-header-inner bg-plum-plate">
                    <div class="menu-header-image"
                    style="background-image: url('/themes/architectui/images/dropdown-header/abstract4.jpg');">
                </div>
                <div class="menu-header-content text-white">
                    <h5 class="menu-header-title">Grid Dashboard</h5>
                    <h6 class="menu-header-subtitle">Easy grid navigation inside dropdowns</h6>
                </div>
            </div>
        </div>
        <div class="grid-menu grid-menu-xl grid-menu-3col">
            <div class="no-gutters row">
                <div class="col-sm-6 col-xl-4">
                    <button
                    class="btn-icon-vertical btn-square btn-transition btn btn-outline-link">
                    <i
                    class="pe-7s-world icon-gradient bg-night-fade btn-icon-wrapper btn-icon-lg mb-3"></i>
                    Automation
                </button>
            </div>
            <div class="col-sm-6 col-xl-4">
                <button
                class="btn-icon-vertical btn-square btn-transition btn btn-outline-link">
                <i
                class="pe-7s-piggy icon-gradient bg-night-fade btn-icon-wrapper btn-icon-lg mb-3"></i>
                Reports
            </button>
        </div>
        <div class="col-sm-6 col-xl-4">
            <button
            class="btn-icon-vertical btn-square btn-transition btn btn-outline-link">
            <i
            class="pe-7s-config icon-gradient bg-night-fade btn-icon-wrapper btn-icon-lg mb-3"></i>
            <?= Yii::t('app', 'Настройки')?>
        </button>
    </div>
    <div class="col-sm-6 col-xl-4">
        <button
        class="btn-icon-vertical btn-square btn-transition btn btn-outline-link">
        <i
        class="pe-7s-browser icon-gradient bg-night-fade btn-icon-wrapper btn-icon-lg mb-3"></i>
        Content
    </button>
</div>
<div class="col-sm-6 col-xl-4">
    <button
    class="btn-icon-vertical btn-square btn-transition btn btn-outline-link">
    <i
    class="pe-7s-hourglass icon-gradient bg-night-fade btn-icon-wrapper btn-icon-lg mb-3"></i>
    Activity
</button>
</div>
<div class="col-sm-6 col-xl-4">
    <button
    class="btn-icon-vertical btn-square btn-transition btn btn-outline-link">
    <i
    class="pe-7s-world icon-gradient bg-night-fade btn-icon-wrapper btn-icon-lg mb-3"></i>
    Contacts
</button>
</div>
</div>
</div>
<ul class="nav flex-column">
    <li class="nav-item-divider nav-item"></li>
    <li class="nav-item-btn text-center nav-item">
        <button class="btn-shadow btn btn-primary btn-sm">Follow-ups</button>
    </li>
</ul>
</div>
</div>
<div class="dropdown">
    <!-- <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="p-0 mr-2 btn btn-link">
        <span class="icon-wrapper icon-wrapper-alt rounded-circle">
            <span class="icon-wrapper-bg bg-danger"></span>
            <i class="icon text-danger icon-anim-pulse ion-android-notifications"></i>
            <span class="badge badge-dot badge-dot-sm badge-danger">Notifications</span>
        </span>
    </button> -->
<div tabindex="-1" role="menu" aria-hidden="true"
class="dropdown-menu-xl rm-pointers dropdown-menu dropdown-menu-right">
<div class="dropdown-menu-header mb-0">
    <div class="dropdown-menu-header-inner bg-deep-blue">
        <div class="menu-header-image opacity-1"
        style="background-image: url('/themes/architectui/images/dropdown-header/city3.jpg');">
    </div>
    <div class="menu-header-content text-dark">
        <h5 class="menu-header-title">Notifications</h5>
        <h6 class="menu-header-subtitle">You have
            <b>21</b> unread messages
        </h6>
    </div>
</div>
</div>
<ul
class="tabs-animated-shadow tabs-animated nav nav-justified tabs-shadow-bordered p-3">
<li class="nav-item">
    <a role="tab" class="nav-link active" data-toggle="tab"
    href="#tab-messages-header">
    <span>Messages</span>
</a>
</li>
<li class="nav-item">
    <a role="tab" class="nav-link" data-toggle="tab" href="#tab-events-header">
        <span>Events</span>
    </a>
</li>
<li class="nav-item">
    <a role="tab" class="nav-link" data-toggle="tab" href="#tab-errors-header">
        <span>System Errors</span>
    </a>
</li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="tab-messages-header" role="tabpanel">
        <div class="scroll-area-sm">
            <div class="scrollbar-container">
                <div class="p-3">
                    <div class="notifications-box">
                        <div
                        class="vertical-time-simple vertical-without-time vertical-timeline vertical-timeline--one-column">
                        <div
                        class="vertical-timeline-item dot-danger vertical-timeline-element">
                        <div>
                            <span
                            class="vertical-timeline-element-icon bounce-in"></span>
                            <div
                            class="vertical-timeline-element-content bounce-in">
                            <h4 class="timeline-title">All Hands Meeting
                            </h4>
                            <span
                            class="vertical-timeline-element-date"></span>
                        </div>
                    </div>
                </div>
                <div
                class="vertical-timeline-item dot-warning vertical-timeline-element">
                <div>
                    <span
                    class="vertical-timeline-element-icon bounce-in"></span>
                    <div
                    class="vertical-timeline-element-content bounce-in">
                    <p>Yet another one, at
                        <span class="text-success">15:00 PM</span>
                    </p>
                    <span
                    class="vertical-timeline-element-date"></span>
                </div>
            </div>
        </div>
        <div
        class="vertical-timeline-item dot-success vertical-timeline-element">
        <div>
            <span
            class="vertical-timeline-element-icon bounce-in"></span>
            <div
            class="vertical-timeline-element-content bounce-in">
            <h4 class="timeline-title">
                Build the production release
                <span
                class="badge badge-danger ml-2">NEW</span>
            </h4>
            <span
            class="vertical-timeline-element-date"></span>
        </div>
    </div>
</div>
<div
class="vertical-timeline-item dot-primary vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <h4 class="timeline-title">
        Something not important
        <div
        class="avatar-wrapper mt-2 avatar-wrapper-overlap">
        <div
        class="avatar-icon-wrapper avatar-icon-sm">
        <div class="avatar-icon">
            <img src="/themes/architectui/images/avatars/1.jpg"
            alt="">
        </div>
    </div>
    <div
    class="avatar-icon-wrapper avatar-icon-sm">
    <div class="avatar-icon">
        <img src="/themes/architectui/images/avatars/2.jpg"
        alt="">
    </div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div class="avatar-icon">
    <img src="/themes/architectui/images/avatars/3.jpg"
    alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div class="avatar-icon">
    <img src="/themes/architectui/images/avatars/4.jpg"
    alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div class="avatar-icon">
    <img src="/themes/architectui/images/avatars/5.jpg"
    alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div class="avatar-icon">
    <img src="/themes/architectui/images/avatars/9.jpg"
    alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div class="avatar-icon">
    <img src="/themes/architectui/images/avatars/7.jpg"
    alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div class="avatar-icon">
    <img src="/themes/architectui/images/avatars/8.jpg"
    alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm avatar-icon-add">
<div class="avatar-icon">
    <i>+</i>
</div>
</div>
</div>
</h4>
<span
class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item dot-info vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <h4 class="timeline-title">This dot has an info
    state</h4>
    <span
    class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item dot-danger vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <h4 class="timeline-title">All Hands Meeting
    </h4>
    <span
    class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item dot-warning vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <p>Yet another one, at
        <span class="text-success">15:00 PM</span>
    </p>
    <span
    class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item dot-success vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <h4 class="timeline-title">
        Build the production release
        <span
        class="badge badge-danger ml-2">NEW</span>
    </h4>
    <span
    class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item dot-dark vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <h4 class="timeline-title">This dot has a dark
    state</h4>
    <span
    class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="tab-pane" id="tab-events-header" role="tabpanel">
    <div class="scroll-area-sm">
        <div class="scrollbar-container">
            <div class="p-3">
                <div
                class="vertical-without-time vertical-timeline vertical-timeline--animate vertical-timeline--one-column">
                <div class="vertical-timeline-item vertical-timeline-element">
                    <div>
                        <span class="vertical-timeline-element-icon bounce-in">
                            <i
                            class="badge badge-dot badge-dot-xl badge-success"></i>
                        </span>
                        <div
                        class="vertical-timeline-element-content bounce-in">
                        <h4 class="timeline-title">All Hands Meeting</h4>
                        <p>
                            Lorem ipsum dolor sic amet, today at
                            <a href="javascript:void(0);">12:00 PM</a>
                        </p>
                        <span class="vertical-timeline-element-date"></span>
                    </div>
                </div>
            </div>
            <div class="vertical-timeline-item vertical-timeline-element">
                <div>
                    <span class="vertical-timeline-element-icon bounce-in">
                        <i
                        class="badge badge-dot badge-dot-xl badge-warning"></i>
                    </span>
                    <div
                    class="vertical-timeline-element-content bounce-in">
                    <p>Another meeting today, at
                        <b class="text-danger">12:00 PM</b>
                    </p>
                    <p>Yet another one, at
                        <span class="text-success">15:00 PM</span>
                    </p>
                    <span class="vertical-timeline-element-date"></span>
                </div>
            </div>
        </div>
        <div class="vertical-timeline-item vertical-timeline-element">
            <div>
                <span class="vertical-timeline-element-icon bounce-in">
                    <i
                    class="badge badge-dot badge-dot-xl badge-danger"></i>
                </span>
                <div
                class="vertical-timeline-element-content bounce-in">
                <h4 class="timeline-title">Build the production
                release</h4>
                <p>
                    Lorem ipsum dolor sit amit,consectetur eiusmdd
                    tempor incididunt ut
                    labore et dolore magna elit enim at minim veniam
                    quis nostrud
                </p>
                <span class="vertical-timeline-element-date"></span>
            </div>
        </div>
    </div>
    <div class="vertical-timeline-item vertical-timeline-element">
        <div>
            <span class="vertical-timeline-element-icon bounce-in">
                <i
                class="badge badge-dot badge-dot-xl badge-primary"></i>
            </span>
            <div
            class="vertical-timeline-element-content bounce-in">
            <h4 class="timeline-title text-success">Something
            not important</h4>
            <p>Lorem ipsum dolor sit amit,consectetur elit enim
            at minim veniam quis nostrud</p>
            <span class="vertical-timeline-element-date"></span>
        </div>
    </div>
</div>
<div class="vertical-timeline-item vertical-timeline-element">
    <div>
        <span class="vertical-timeline-element-icon bounce-in">
            <i
            class="badge badge-dot badge-dot-xl badge-success"></i>
        </span>
        <div
        class="vertical-timeline-element-content bounce-in">
        <h4 class="timeline-title">All Hands Meeting</h4>
        <p>
            Lorem ipsum dolor sic amet, today at
            <a href="javascript:void(0);">12:00 PM</a>
        </p>
        <span class="vertical-timeline-element-date"></span>
    </div>
</div>
</div>
<div class="vertical-timeline-item vertical-timeline-element">
    <div>
        <span class="vertical-timeline-element-icon bounce-in">
            <i
            class="badge badge-dot badge-dot-xl badge-warning"></i>
        </span>
        <div
        class="vertical-timeline-element-content bounce-in">
        <p>Another meeting today, at
            <b class="text-danger">12:00 PM</b>
        </p>
        <p>Yet another one, at
            <span class="text-success">15:00 PM</span>
        </p>
        <span class="vertical-timeline-element-date"></span>
    </div>
</div>
</div>
<div class="vertical-timeline-item vertical-timeline-element">
    <div>
        <span class="vertical-timeline-element-icon bounce-in">
            <i
            class="badge badge-dot badge-dot-xl badge-danger"></i>
        </span>
        <div
        class="vertical-timeline-element-content bounce-in">
        <h4 class="timeline-title">Build the production
        release</h4>
        <p>
            Lorem ipsum dolor sit amit,consectetur eiusmdd
            tempor incididunt ut
            labore et dolore magna elit enim at minim veniam
            quis nostrud
        </p>
        <span class="vertical-timeline-element-date"></span>
    </div>
</div>
</div>
<div class="vertical-timeline-item vertical-timeline-element">
    <div>
        <span class="vertical-timeline-element-icon bounce-in">
            <i
            class="badge badge-dot badge-dot-xl badge-primary"></i>
        </span>
        <div
        class="vertical-timeline-element-content bounce-in">
        <h4 class="timeline-title text-success">Something
        not important</h4>
        <p>Lorem ipsum dolor sit amit,consectetur elit enim
        at minim veniam quis nostrud</p>
        <span class="vertical-timeline-element-date"></span>
    </div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="tab-pane" id="tab-errors-header" role="tabpanel">
    <div class="scroll-area-sm">
        <div class="scrollbar-container">
            <div class="no-results pt-3 pb-0">
                <div class="swal2-icon swal2-success swal2-animate-success-icon">
                    <div class="swal2-success-circular-line-left"
                    style="background-color: rgb(255, 255, 255);"></div>
                    <span class="swal2-success-line-tip"></span>
                    <span class="swal2-success-line-long"></span>
                    <div class="swal2-success-ring"></div>
                    <div class="swal2-success-fix"
                    style="background-color: rgb(255, 255, 255);"></div>
                    <div class="swal2-success-circular-line-right"
                    style="background-color: rgb(255, 255, 255);"></div>
                </div>
                <div class="results-subtitle">All caught up!</div>
                <div class="results-title">There are no system errors!</div>
            </div>
        </div>
    </div>
</div>
</div>
    <ul class="nav flex-column">
        <li class="nav-item-divider nav-item"></li>
        <li class="nav-item-btn text-center nav-item">
            <button class="btn-shadow btn-wide btn-pill btn btn-focus btn-sm">View Latest
            Changes</button>
        </li>
    </ul>
</div>
</div>
<div class="dropdown">
    <button type="button" data-toggle="dropdown" class="p-0 mr-2 btn btn-link">
        <span class="icon-wrapper icon-wrapper-alt rounded-circle">
            <span class="icon-wrapper-bg bg-focus"></span>
            <?php $lang = Yii::$app->language; ?>
            <?php $langIcon = 'UZ'; ?>
            <?php if ($lang == 'uz'): ?>
                <?php $langIcon = 'UZ'; ?>
            <?php elseif ($lang == 'ru'): ?>
                <?php $langIcon = 'RU'; ?>
            <?php elseif ($lang == 'en'): ?>
                <?php $langIcon = 'US'; ?>
            <?php endif ?>
            <span class="language-icon opacity-8 flag large <?= $langIcon ?>"></span>
        </span>
    </button>
    <div tabindex="-1" role="menu" aria-hidden="true"
    class="rm-pointers dropdown-menu dropdown-menu-right">
    <div class="dropdown-menu-header">
        <div class="dropdown-menu-header-inner pt-3 pb-4 bg-focus">
            <div class="menu-header-image opacity-05"
            style="background-image: url('/themes/architectui/images/dropdown-header/city2.jpg');">
        </div>
        <div class="menu-header-content text-center text-white">
            <h6 class="menu-header-subtitle mt-0">
                Tilni o'zgartirish<br>
                Изменить язык<br>
                Change language<br>
            </h6>
        </div>
    </div>
</div>
<h6 tabindex="-1" class="dropdown-header text-center">
    TILLAR ЯЗЫКИ LANGUAGES
</h6>
<?php $lang = Yii::$app->language; ?>
<?= Html::a('<span class="mr-3 opacity-8 flag large UZ"></span> Oʻzbekcha', array_merge(
    \Yii::$app->request->get(),
    [\Yii::$app->controller->route, 'language' => 'uz']
),
[
    'class' => 'dropdown-item' . (($lang == 'uz') ? ' active' : ''),
]
); ?>
<?= Html::a('<span class="mr-3 opacity-8 flag large RU"></span> Русский', array_merge(
    \Yii::$app->request->get(),
    [\Yii::$app->controller->route, 'language' => 'ru']
),
[
    'class' => 'dropdown-item' . (($lang == 'ru') ? ' active' : ''),
]
); ?>
<?= Html::a('<span class="mr-3 opacity-8 flag large US"></span> English', array_merge(
    \Yii::$app->request->get(),
    [\Yii::$app->controller->route, 'language' => 'en']
),
[
    'class' => 'dropdown-item' . (($lang == 'en') ? ' active' : ''),
]
); ?>
</div>
</div>
<div class="dropdown">
    <?php if (isset($countBarcodes) and $countBarcodes > 0): ?>
        <a href="<?= url('/ttn/alarm');?>">
            <button type="button" class="p-0 btn btn-link dd-chart-btn">
                <span class="icon-wrapper icon-wrapper-alt rounded-circle">
                    <span class="icon-wrapper-bg bg-danger"></span>
                    <i class="fas fa-bell icon text-danger icon-anim-pulse"></i>
                </span>
            </button>
        </a>
    <?php endif; ?>

    <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu-xl rm-pointers dropdown-menu dropdown-menu-right">
        <div class="dropdown-menu-header">
            <div class="dropdown-menu-header-inner bg-premium-dark">
                <div class="menu-header-image" style="background-image: url('/themes/architectui/images/dropdown-header/abstract4.jpg');"></div>
                <div class="menu-header-content text-white">
                    <h5 class="menu-header-title"><?= Yii::t('app','Пользователи онлайн')?></h5>
                    <h6 class="menu-header-subtitle"><?= Yii::t('app','Обзор последних действий в учетной записи')?></h6>
                </div>
            </div>
        </div>
    <div class="widget-chart">
        <div class="widget-chart-content">
            <div class="icon-wrapper rounded-circle">
                <div class="icon-wrapper-bg opacity-9 bg-focus"></div>
                <i class="lnr-users text-white"></i>
            </div>
            <div class="widget-numbers">
                <span>344k</span>
            </div>
            <div class="widget-subheading pt-2">
                <?= Yii::t('app', 'Просмотры профиля с момента последнего входа')?>
            </div>
            <div class="widget-description text-danger">
                <span class="pr-1">
                    <span>176%</span>
                </span>
                <i class="fa fa-arrow-left"></i>
            </div>
        </div>
        <div class="widget-chart-wrapper">
            <div id="dashboard-sparkline-carousel-3-pop"></div>
        </div>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item-divider mt-0 nav-item"></li>
        <li class="nav-item-btn text-center nav-item">
            <button class="btn-shine btn-wide btn-pill btn btn-warning btn-sm">
                <i class="fa fa-cog fa-spin mr-2"></i>
                <?= Yii::t('app', 'Посмотреть детали')?>
            </button>
        </li>
    </ul>
    </div>
</div>
</div>
<div class="header-btn-lg pr-0">
    <div class="widget-content p-0">
        <div class="widget-content-wrapper">
            <div class="widget-content-left header-user-info text-right">
                <div class="widget-heading"> <?= $identity->fio ?></div>
                <div class="widget-subheading">
                    <?= $regionName ?><?= ($regionName) ? (', ' . $branchName) : $branchName ?>
                </div>
            </div>
            <div class="widget-content-left ml-3">
                <div class="btn-group">
                    <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    class="p-0 btn">
                    <?php if(empty($img_src) or is_file(Yii::getAlias('@webroot')."/images/employee/$img_src")==false):?>
                    <img width="42" class="rounded-circle" style="border: 2px solid #FFFFFF"
                    src="/themes/architectui/images/avatars/placeholder.png" alt="">
                <?php else:?>
                    <img width="42" height="42" class="rounded-circle"
                    style="border: 2px solid #FFFFFF; object-fit:cover;"
                    src="/images/employee/<?= $img_src?>" alt="Img Upload">
                <?php endif;?>
                <i class="fa fa-angle-down ml-2 opacity-8"></i>
            </a>
            <div tabindex="-1" role="menu" aria-hidden="true"
            class="rm-pointers dropdown-menu-lg dropdown-menu dropdown-menu-right">
            <div class="dropdown-menu-header">
                <div class="dropdown-menu-header-inner bg-asteroid">
                    <div class="menu-header-image opacity-2"
                    style="background-image: url('/themes/architectui/images/dropdown-header/city3.jpg');">
                </div>
                <div class="menu-header-content text-left">
                    <div class="widget-content p-0">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left mr-3">
                                <?php if(empty($img_src) or is_file(Yii::getAlias('@webroot')."/images/employee/$img_src") == false):?>
                                <img width="72" class="rounded-circle"
                                style="border: 2px solid #FFFFFF"
                                src="/themes/architectui/images/avatars/placeholder.png"
                                alt="">
                            <?php else:?>
                                <img width="72" height="72" class="rounded-circle"
                                style="border: 2px solid #FFFFFF; object-fit:cover;"
                                src="/images/employee/<?= $img_src?>" alt="">
                            <?php endif;?>
                        </div>
                        <div class="widget-content-left">
                            <div class="widget-heading" style="color: #FFFFFF;">
                                <?php 
                                $pos1 = strpos($identity->fio, ' ');
                                $pos2 = strpos($identity->fio, ' ', $pos1 + 1);
                                ?>
                                <?= my_mb_ucfirst(substr($identity->fio, 0, $pos2))?></div>
                                <div class="widget-subheading opacity-8"
                                style="color: #FFFFFF;">
                                <?= $regionName ?><?= ($regionName) ? ('<br>' . $branchName) : $branchName ?>
                            </div>
                        </div>
                        <div class="widget-content-right mr-2">
                            <a href="<?= Url::to(["/site/logout"]) ?>">
                                <button
                                class="btn-pill btn-shadow btn-shine btn btn-primary">
                                <?= Yii::t('app','Выход')?>
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                                            <!-- <div class="scroll-area-xs" style="height: 150px;">
                                                    <div class="scrollbar-container ps">
                                                        <ul class="nav flex-column">
                                                            <li class="nav-item-header nav-item">Регион</li>
                                                            <li class="nav-item">
                                                                <a href="javascript:void(0);" class="nav-link">
                                                                    <?= $regionName ?>
                                                                    <div class="ml-auto badge badge-pill badge-info">8</div>
                                                                </a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a href="javascript:void(0);" class="nav-link">Recover Password</a>
                                                            </li>
                                                            <li class="nav-item-header nav-item">
                                                                My Account
                                                            </li>
                                                            <li class="nav-item">
                                                                <a href="javascript:void(0);" class="nav-link">
                                                                    Settings
                                                                    <div class="ml-auto badge badge-success">New</div>
                                                                </a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a href="javascript:void(0);" class="nav-link">
                                                                    Messages
                                                                    <div class="ml-auto badge badge-warning">512</div>
                                                                </a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a href="javascript:void(0);" class="nav-link">Logs</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div> -->
                                            <!-- <ul class="nav flex-column">
                                                    <li class="nav-item-divider mb-0 nav-item"></li>
                                                </ul> -->
                                                <div class="grid-menu grid-menu-2col">
                                                    <div class="no-gutters row">
                                                        <div class="col-sm-6">
                                                            <button
                                                            class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-dark">
                                                            <i class="lnr-user btn-icon-wrapper mb-2"
                                                            style="font-weight: bold;"></i>
                                                            <b>
                                                                <?php $pos1 = strpos($userName, ' '); $pos2 = strpos($userName,' ',  $pos1+1); echo my_mb_ucfirst(substr($userName, 0, $pos2)); ?>
                                                            </b>
                                                        </button>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <button
                                                        class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-dark">
                                                        <i class="pe-7s-user btn-icon-wrapper mb-2"
                                                        style="font-weight: bold;"></i>
                                                        <b><?= my_mb_ucfirst(mb_strtolower($loginName)) ?></b>
                                                    </button>
                                                </div>
                                                <div class="col-sm-6">
                                                    <button
                                                    class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-dark">
                                                    <i class="fa fa-globe btn-icon-wrapper mb-2"
                                                    style="font-weight: bold;"></i>
                                                    <b><?= my_mb_ucfirst(mb_strtolower($regionName)) ?></b>
                                                </button>
                                            </div>
                                            <div class="col-sm-6">
                                                <button
                                                class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-dark">
                                                <i class="fas fa-building btn-icon-wrapper mb-2"></i>
                                                <b><?= str_replace('bts', 'BTS', my_mb_ucfirst(mb_strtolower($branchName))) ?></b>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="app-main">
    <div class="app-sidebar sidebar-shadow bg-asteroid sidebar-text-light">
        <div class="app-header__logo">
            <div class="logo-src"></div>
            <div class="header__pane ml-auto">
                <div>
                    <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                    data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
        <span>
            <button type="button"
            class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
            <span class="btn-icon-wrapper">
                <i class="fa fa-ellipsis-v fa-w-6"></i>
            </span>
        </button>
    </span>
</div>
<div class="scrollbar-sidebar">
    <div class="app-sidebar__inner">
        <?php
        $menuGeneral = [
            [
                'label' => "<i class='metismenu-icon fa fa-home'></i> <span class='name'>".Yii::t('app', 'Главная')."</span>",
                'url' => ["/monitoring/home"],
                'order' => 1
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app', 'Накладная')."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app', 'Новый'),
                        'url' => ["/site/update"]
                    ],
                    [
                        'label' => Yii::t('app', 'Список Накладной'),
                        'url' => ["/site/index", "list" => 1]
                    ]
                ],
                'order' => 2
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-off'></i> <span class='name'>".Yii::t('app',"Выход")." (<span style='text-transform: uppercase;'>" . $identity->username . "</span>)</span>",
                'url' => ["/site/logout"],
                'order' => 100
            ]
        ];

        $menuHumanResources = [
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>Персонал</span>",
                'items' => [
                    [
                        'label' => Yii::t('app', 'Сотрудники'),
                        'url' => ["/employee/index"]
                    ],
                    [
                        'label' => Yii::t('app', 'СПИСОК СОТРУДНИКОВ'),
                        'url' => ["/employee/staff"]
                    ],
                    [
                        'label' => Yii::t('app', 'ОРГ СТРУКТУРА'),
                        'url' => ['/employee/position-index']
                    ],
                    [
                        'label'=>Yii::t('app',"Пользователие"),
                        'url'=>['/users/']
                    ]
                ],
                'order' => 4
            ]
        ];

        $menuCustomer = [
            [
                'label' => "<i class='metismenu-icon fa fa-home'></i> <span class='name'>".Yii::t('app', 'Отчет')."</span>",
                'url' => ["/customer/debit", "id" => $identity->customerId],
                'order' => 3
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-wechat'></i> <span class='name'>".Yii::t('app', 'Портал')."</span>",
                'url' => ["/site/chat"],
                'order' => 4
            ],
        ];

        $menuDivision = [
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>KPI</span>",
                'items' => [
                    [
                        'label' => Yii::t('app', "РЕГИОНАЛЬНЫЙ МАНАЖЕР"),
                        'url' => ["/report/kpi-manager"]
                    ],
                    [
                        'label' => Yii::t('app', "COURIER PERFORMANCE (KPI)"),
                        'url' => ["/report/bonus"]
                    ],
                    [
                        'label' => Yii::t('app', "ПОКАЗАТЕЛИ РАБОТЫ СОТРУДНИКОВ (KPI)"),
                        'url' => ["/report/kpi"]
                    ]
                ],
                'order' => 4
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app', 'Отчет')."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app', 'Главная'),
                        'url' => ["/report/sale"]
                    ],
                    [
                        'label' => Yii::t('app', "Международная"),
                        'url' => ["/report/sale-international"]
                    ],
                ],
                'order' => 5
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app','Инкасса')."</span>",
                'url' => ["/report/collection"],
                'order' => 6
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app','Электронная коммерция')."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app','Инкасса'),
                        'url' => ["/report/ecom-sum-day-by-branch"]
                    ],
                    [
                        'label' => Yii::t('app','ТМЦ'),
                        'url' => ["/report/ecom-warehouse"]
                    ],
                ],
                'order' => 7
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app', "Движениe почты")."</span>",
                'url' => ["/site/movement"],
                'order' => 8
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app', 'Настройки')."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app','Курьеры'),
                        'url' => ["/courier/index"]
                    ],
                    [
                        'label' => Yii::t('app', "Пользователи"),
                        'url' => ["/users/index"]
                    ]
                ],
                'order' => 9
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app','ТМЦ')."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app', "ПРИНЯТЬ ТМЦ"),
                        'url' => ["/tms/transportation"]
                    ],
                    [
                        'label' => Yii::t('app', "ОТЧЕТ ПО ТМЦ"),
                        'url' => ["/tms/index"]
                    ]
                ],
                'order' => 9
            ],
        ];

        $menuAgent = [
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>".Yii::t('app',"Принять на склад")."</span>",
                'url' => ["/ttn/from-transport-to-warehouset"],
                'order' => 3
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>Принять почту</span>",
                'url' => ["/site/receive-nakladnoy"],
                'order' => 4
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>".Yii::t('app', "Склад")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app', "Продажа упаковки"),
                        'url' => ["/package/sold"]
                    ],
                    [
                        'label' => Yii::t('app', "Инвентаризация"),
                        'url' => ["/waybill/receive-balance"]
                    ],
                    [
                        'label' => Yii::t('app', "Двежения отправлений") ,
                        'url' => ["/waybill/ostatok", "list" => 1]
                    ]
                ],
                'order' => 5
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-retweet'></i> <span class='name'>".Yii::t('app', "Переотправить смс")."</span>",
                'url' => ["/site/resend-sms"],
                'order' => 6
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-retweet'></i> <span class='name'>".Yii::t('app', "Cдавать почту")."</span>",
                'url' => ["/site/give-nakladnoy"],
                'order' => 7
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-retweet'></i> <span class='name'>".Yii::t('app', "Cписок сдавать почты")."</span>",
                'url' => ["/site/hand-over-list"],
                'order' => 8
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-road'></i> <span class='name'>".Yii::t('app', 'Отдать Курьеру')."</span>",
                'url' => ["/site/give-nakladnoy-kuryer"],
                'order' => 9
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-random'></i> <span class='name'>".Yii::t('app', "Движениe почты")."</span>",
                'url' => ["/site/movement"],
                'order' => 10
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-open'></i> <span class='name'>".Yii::t('app', "Накладной мешок")."</span>",
                'url' => ["/control/index"],
                'order' => 11
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-automobile'></i> <span class='name'>".Yii::t('app', "Отправка грузов")."</span>",
                'url' => ["/cargo/index"],
                'order' => 12
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-fax'></i> <span class='name'>".Yii::t('app', "Инкасса")."</span>",
                'url' => ["/report/collection"],
                'order' => 13
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-fax'></i> <span class='name'>".Yii::t('app', "KPI")."</span>",
                'url' => ["/report/kpi"],
                'order' => 14
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>".Yii::t('app', "Электронная коммерция")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app', "Инкасса"),
                        'url' => ["/report/ecommercy-by-branch"]
                    ],
                    [
                        'label' => Yii::t('app', "ТМЦ"),
                        'url' => ["/report/ecom-warehouse"]
                    ],
                ],
                'order' => 15
            ],
        ];

        $menuLeadCourier = [
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>".Yii::t('app', "Курьеры")."</span>",
                'url' => ["/site/receive-lead-courier"],
                'order' => 3
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>".Yii::t('app', "Отдать Курьеру")."</span>",
                'url' => ["/site/give-nakladnoy-kuryer"],
                'order' => 4
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>".Yii::t('app', "Курьеры")."</span>",
                'url' => ["/courier/index"],
                'order' => 5
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-exclamation-circle'></i> <span class='name'>".Yii::t('app', "Не доставленные")."</span>",
                'url' => ["/site/kuryer-not-delivered"],
                'order' => 6
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-money'></i> <span class='name'>ПК (KPI)</span>",
                'url' => ["/report/bonus"],
                'order' => 7
            ],
        ];

        $menuWarehouse = [
            [
                'label' => "<i class='metismenu-icon fa fa-exclamation-circle'></i> <span class='name'>".Yii::t('app',"Принять в РЦ")."</span>",
                'url' => ["/site/distribution-center"],
                'order' => 3
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-road'></i> <span class='name'>".Yii::t('app', "Отдать Курьеру")."</span>",
                'url' => ["/site/give-nakladnoy-kuryer"],
                'order' => 4
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>".Yii::t('app',"Отдать Курьерка")."</span>",
                'url' => ["/site/give-waybill", "status" => 3],
                'order' => 5
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>".Yii::t('app',"Мешок")."</span>",
                'url' => ["/qop/qop"],
                'order' => 6
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-folder-open'></i> <span class='name'>".Yii::t('app',"Накладной -мешок")."</span>",
                'url' => ["/control/index"],
                'order' => 7
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-automobile'></i> <span class='name'>".Yii::t('app',"Отправка грузов")."</span>",
                'url' => ["/cargo/index"],
                'order' => 8
            ],
        ];

        $menuHouseHould = [
            [
                'label' => "<i class='metismenu-icon fa fa-exclamation-circle'></i> <span class='name'>".Yii::t('app',"ПОСТУПЛЕНИЯ ТМЦ")."</span>",
                'url' => ["/tms/create"],
                'order' => 3
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-exclamation-circle'></i> <span class='name'>".Yii::t('app',"ПЕРЕМЕЩЕНИЕ ТМЦ")."</span>",
                'url' => ["/tms/transportation"],
                'order' => 4
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-exclamation-circle'></i> <span class='name'>".Yii::t('app',"ОСТАТОК ТМЦ")."</span>",
                'url' => ["/tms/index"],
                'order' => 5
            ],
        ];
        $menuCollection = [
            [
                'label' => "<i class='metismenu-icon fa fa-fax'></i> <span class='name'>".Yii::t('app',"Касса")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Инкасса"),
                        'url' => ["/report/collection"]
                    ],
                    [
                        'label' => Yii::t('app',"Инкасса назорати"),
                        'url' => ["/collection/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Бухгалтер"),
                        'url' => ["/collection/cashier-list"]
                    ]
                ],
                'order' => 3
            ],
        ];
        $menuInternational = [
            [
                'label' => "<i class='metismenu-icon fa fa-bar-chart-o'></i> <span class='name'>".Yii::t('app',"Отчет")."</span>",
                'url' => ["/report/international"],
                'order' => 3
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-fax'></i> <span class='name'>".Yii::t('app',"Страны")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Города страны"),
                        'url' => ["/country-city/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Страна - Зона"),
                        'url' => ["/zona-international/country"]
                    ],
                ],
                'order' => 4
            ],
        ];

        $menuCallDisp = [
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>".Yii::t('app',"Список заявок")."</span>",
                'url' => ["/waybill/order-list"],
                'order' => 3
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>".Yii::t('app',"Список заявок")."</span>",
                'url' => ["/waybill/order-list"],
                'order' => 4
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-automobile'></i> <span class='name'>".Yii::t('app',"Мониторинг")."</span>",
                'url' => ["/monitoring/waybills"],
                'order' => 5
            ],
        ];

        $menuAccountant = [
            [
                'label' => "<i class='metismenu-icon fa fa-fax'></i> <span class='name'>".Yii::t('app',"Страны")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Список накладной"),
                        'url' => ["/waybill/report"]
                    ],
                    [
                        'label' => "KPI",
                        'url' => ["/report/kpi"]
                    ],
                ],
                'order' => 4
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-bar-chart-o'></i> <span class='name'>".Yii::t('app',"Отчет")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Главная"),
                        'url' => ["/report/sale"],
                    ],
                    [
                        'label' => Yii::t('app',"Пифагор"),
                        'url' => ["/report/pifagor"],
                    ],
                    [
                        'label' => Yii::t('app',"Международная"),
                        'url' => ["/report/international"],
                    ],
                    [
                        'label' => Yii::t('app',"ABC"),
                        'url' => ["/otchot/abc", "year" => date('Y')],
                    ],
                    [
                        'label' => Yii::t('app',"Инкасса"),
                        'url' => ["/report/collection"],
                    ],
                ],
                'order' => 5
            ],
        ];

        $menuSales = [
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app',"Клиенты")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Клиенты"),
                        'url' => ["/customer/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Старые контракты"),
                        'url' => ["/customer/contract-old"]
                    ],
                    [
                        'label' => Yii::t('app',"Остаток на начало"),
                        'url' => ["/customer/ostatok"]
                    ],
                    [
                        'label' => Yii::t('app',"Оплата"),
                        'url' => ["/customer/payment"]
                    ],
                ],
                'order' => 3
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app',"Отчет")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"ABC"),
                        'url' => ["/otchot/abc"]
                    ],
                    [
                        'label' => Yii::t('app',"ABC Долг"),
                        'url' => ["/otchot/abc-debit", "year" => date('Y')]
                    ],
                    [
                        'label' => Yii::t('app',"Баланс клиентов"),
                        'url' => ["/otchot/receivables"]
                    ]
                ],
                'order' => 4
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-envelope'></i> <span class='name'>".Yii::t('app',"Доступ клиентам")."</span>",
                'url' => ["/users/index"],
                'order' => 5
            ],
        ];

        $menuEcom = [
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app',"Клиенты")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Клиенты"),
                        'url' => ["/customer/index"]
                    ]
                ],
                'order' => 3
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app','Электронная коммерция')."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"ТМЦ"),
                        'url' => ["/report/ecom-warehouse"]
                    ],
                    [
                        'label' => Yii::t('app', 'НАЛОЖЕННЫЙ ПЛАТЕЖ'),
                        'url' => ["/report/ecom-sum-day-by-branch"]
                    ],
                    [
                        'label' => Yii::t('app', "УНИВЕРСАЛЬ БАНК"),
                        'url' => ["/bank/universal-bank-money"]
                    ],
                    [
                        'label' => Yii::t('app', "ВОЗВРАТ ТОВАРОВ"),
                        'url' => ["/customer/give-returned-waybill"]
                    ],
                ],
                'order' => 4
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app',"Отчет")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app','Касса'),
                        'url' => ["/report/ecommercy-cash"]
                    ],
                    [
                        'label' => Yii::t('app',"ABC Долг"),
                        'url' => ["/otchot/abc-debit", "year" => date('Y')]
                    ],
                ],
                'order' => 5
            ],
        ];

        $menuAdmin = [
            [
                'label' => "<i class='metismenu-icon fa fa-suitcase'></i> <span class='name'>".Yii::t('app','Клиенты')."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Клиенты"),
                        'url' => ["/customer/index"]
                    ],
                    [
                        'label' => Yii::t('app','Старые контракты'),
                        'url' => ["/customer/contract-old"]
                    ],
                    [
                        'label' => Yii::t('app','Остаток на начало'),
                        'url' => ["/customer/ostatok"]
                    ],
                    [
                        'label' => Yii::t('app','Оплата'),
                        'url' => ["/customer/payment"]
                    ],
                    [
                        'label' => Yii::t('app',"Рассылки"),
                        'url' => ["/newsletter/index"]
                    ],
                    [
                        'label' => Yii::t('app','Встреча с клиентами'),
                        'url' => ["/customer-meeting/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Счет-фактуры"),
                        'url' => ["/invoice/number"]
                    ],
                    [
                        'label' => Yii::t('app', "Список дог."),
                        'url' => ["/cost-2019/index"]
                    ],
                ],
                'order' => 3
            ],
            [
                'label' => "<i class='metismenu-icon fas fa-users'></i> <span class='name'>" .Yii::t('app', "Отдел Кадров"). "</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Сотрудники"),
                        'url' => ["/employee/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Курьеры"),
                        'url' => ["/courier/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Шоферы"),
                        'url' => ["/driver/index"]
                    ],
                    [
                        'label' => Yii::t('app',"OРГ СТРУКТУРА"),
                        'url' => ["/employee/position-index"]
                    ],
                    
                    [
                        'label' => Yii::t('app',"Отправка грузов"),
                        'url' => ["/cargo/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Учет премии"),
                        'url' => ["/report/salary-driver"]
                    ]
                ],
                'order' => 4
            ],

            [
                'label' => "<i class='metismenu-icon fa fa-bar-chart-o'></i> <span class='name'>".Yii::t('app',"Отчет")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Главная"),
                        'url' => ["/report/sale"],
                    ],
                    [
                        'label' => Yii::t('app',"Пифагор"),
                        'url' => ["/report/pifagor"],
                    ],
                    [
                        'label' => Yii::t('app',"Анализ продаж"),
                        'url' => ["/report/sale-services"],
                    ],
                    [
                        'label' => Yii::t('app',"B2B, B2C"),
                        'url' => ["/report/sale-by-more"],
                    ],
                    [
                        'label' => Yii::t('app',"ФЕРМЕРЫ"),
                        'url' => ["/customer/sale-group"],
                    ],
                    [
                        'label' => Yii::t('app',"Инкасса"),
                        'url' => ["/report/collection"],
                    ],
                    [
                        'label' => Yii::t('app',"Международная"),
                        'url' => ["/report/international"],
                    ],
                    [
                        'label' => Yii::t('app',"ABC"),
                        'url' => ["/otchot/abc", "year" => date('Y')],
                    ],
                    [
                        'label' => Yii::t('app',"ABC Долг"),
                        'url' => ["/otchot/abc-debit", "year" => date('Y')],
                    ],
                    [
                        'label' => Yii::t('app',"Баланс клиентов"),
                        'url' => ["/otchot/receivables"],
                    ],
                    [
                        'label' => Yii::t('app',"Объем"),
                        'url' => ["/report/sale-weight", "year" => date('Y')],
                    ],
                    [
                        'label' => Yii::t('app',"Отчет клиента"),
                        'url' => ["/report/sale-by-more"],
                    ],
                    [
                        'label' => Yii::t('app',"По сотрудникам"),
                        'url' => ["/report/kpi"],
                    ],
                    [
                        'label' => Yii::t('app',"Ежедневный отчет"),
                        'url' => ["/report/daily-waybill"],
                    ],
                    [
                        'label' => Yii::t('app',"ОТК"),
                        'url' => ["/kpi-dc-fines/index"],
                    ],
                    [
                        'label' => Yii::t('app',"KPI МАНАЖЕР"),
                        'url' => ["/report/kpi-manager"],
                    ],
                ],
                'order' => 7
            ],
            [
                'label' => "<i class='metismenu-icon far fa-handshake'></i> <span class='name'>".Yii::t('app',"ФРАНЧАЙЗИНГ")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Настройки"),
                        'url' => ["/franchising/settings"]
                    ],
                    [
                        'label' => Yii::t('app',"Отчет"),
                        'url' => ["/franchising/act-index"]
                    ],
                    [
                        'label' => Yii::t('app',"Баланс"),
                        'url' => ["/franchising/balance"]
                    ],
                    [
                        'label' => Yii::t('app',"Статистика"),
                        'url' => ["/franchising/statistics"]
                    ],

                ],
                'order' => 8
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>".Yii::t('app',"Электронная коммерция")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"ТМЦ"),
                        'url' => ["/report/ecom-warehouse"]
                    ],
                    [
                        'label' => Yii::t('app',"НАЛОЖЕННЫЙ ПЛАТЕЖ"),
                        'url' => ["/report/ecom-sum-day-by-branch"]
                    ],
                    [
                        'label' => Yii::t('app',"УНИВЕРСАЛЬ БАНК"),
                        'url' => ["/bank/universal-bank-money"]
                    ],
                    [
                        'label' => Yii::t('app',"ВОЗВРАТ ТОВАРОВ"),
                        'url' => ["/customer/give-returned-waybill"]
                    ],
                ],
                'order' => 4
            ],
            [
                'label' => "<i class='metismenu-icon far fa-chart-bar'></i> <span class='name'>".Yii::t('app',"Мониторинг")."</span>",
                'url' => ["/monitoring/waybills"],
                'order' => 9
            ],
            [
                'label' => "<i class='metismenu-icon fas fa-warehouse'></i> <span class='name'>".Yii::t('app',"ТМЦ ЦЕНТР. СКЛАД")."</span>",
                'items' => [
                                            // [
                                            //  'label' => "ПОСТУПЛЕНИЯ ТМЦ",
                                            //  'url' => ["/tms/create"]
                                            // ],
                    [
                        'label' => Yii::t('app',"ПЕРЕМЕЩЕНИЕ ТМЦ"),
                        'url' => ["/tms/transportation"]
                    ],
                    [
                        'label' => Yii::t('app',"ОСТАТОК ТМЦ"),
                        'url' => ["/tms/report2"]
                    ],
                ],
                'order' => 10
            ],
            [
                'label'=>"<i class='metismenu-icon fas fa-plus'></i> <span  class='name'>".Yii::t('app',"Новости")."</span> ",
                'url'=>['/newss'],
                'order'=>11
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-cog'></i> <span class='name'>".Yii::t('app',"Настройки")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Город"),
                        'url' => ["/city/index"],
                    ],
                    [
                        'label' => Yii::t('app',"Офисы"),
                        'url' => ["/branch/index"],
                    ],
                    [
                        'label' => Yii::t('app',"Пользователи"),
                        'url' => ["/users/index"]
                    ],
                ],
                'order' => 12
            ],
                                    // [
                                    //  'label' => "<i class='metismenu-icon far fa-plus-square'></i> <span class='name'>Добавить KPI Курьера</span>",
                                    //  'url' => ["/employee-bonus/create"],
                                    //  'order' => 11
                                    // ],
        ];

        $menuSuperAdmin = [
            [
                'label' => "<i class='metismenu-icon fa fa-suitcase'></i> <span class='name'>".Yii::t('app',"Клиенты")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Клиенты"),
                        'url' => ["/customer/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Старые контракты"),
                        'url' => ["/customer/contract-old"]
                    ],
                    [
                        'label' => Yii::t('app',"Остаток на начало"),
                        'url' => ["/customer/ostatok"]
                    ],
                    [
                        'label' => Yii::t('app',"Оплата"),
                        'url' => ["/customer/payment"]
                    ],
                    [
                        'label' => Yii::t('app',"Рассылки"),
                        'url' => ["/newsletter/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Встреча с клиентами"),
                        'url' => ["/customer-meeting/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Счет-фактуры"),
                        'url' => ["/invoice/number"]
                    ],
                    [
                        'label' => Yii::t('app',"Список дог."),
                        'url' => ["/cost-2019/index"]
                    ],
                ],
                'order' => 3
            ],
            [
                'label' => "<i class='metismenu-icon fas fa-users'></i> <span class='name text-capitalize'>".Yii::t('app',"Отдел Кадров")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Сотрудники"),
                        'url' => ["/employee/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Курьеры"),
                        'url' => ["/courier/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Шоферы"),
                        'url' => ["/driver/index"]
                    ],
                    [
                        'label' => Yii::t('app',"OРГ СТРУКТУРА"),
                        'url' => ["/employee/position-index"]
                    ],

                    [
                        'label' => Yii::t('app',"Отправка грузов"),
                        'url' => ["/cargo/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Учет премии"),
                        'url' => ["/report/salary-driver"]
                    ]
                ],
                'order' => 4
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-money'></i> <span class='name'>".Yii::t('app',"Цены")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Физическое лицо"),
                        'url' => ["/cost-person/index"],
                    ],
                    [
                        'label' => Yii::t('app',"Шаблон"),
                        'url' => ["/cost-default/index"],
                    ],
                    [
                        'label' => Yii::t('app', "Зона"),
                        'url' => ["/zona"],
                    ],
                    [
                        'label' => Yii::t('app',"Город - зона"),
                        'url' => ["/zona/city"],
                    ],
                ],
                'order' => 5
            ],

            [
                'label' => "<i class='metismenu-icon fa fa-fax'></i> <span class='name'>".Yii::t('app',"Касса")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Главный счет"),
                        'url' => ["/category-transfer"]
                    ],
                    [
                        'label' => Yii::t('app',"Суб счет"),
                        'url' => ["/sub-category-transfer"]
                    ],
                    [
                        'label' => Yii::t('app',"Инкасса"),
                        'url' => ["/report/collection"]
                    ]
                ],
                'order' => 6
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-bar-chart-o'></i> <span class='name'>".Yii::t('app',"Отчет")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Главная"),
                        'url' => ["/report/sale"],
                    ],
                    [
                        'label' => Yii::t('app',"Пифагор"),
                        'url' => ["/report/pifagor"],
                    ],
                    [
                        'label' => Yii::t('app',"Анализ продаж"),
                        'url' => ["/report/sale-services"],
                    ],
                    [
                        'label' => Yii::t('app',"ФЕРМЕРЫ"),
                        'url' => ["/customer/sale-group"],
                    ],
                    [
                        'label' => Yii::t('app',"Международная"),
                        'url' => ["/report/international"],
                    ],
                    [
                        'label' => Yii::t('app',"ABC"),
                        'url' => ["/otchot/abc", "year" => date('Y')],
                    ],
                    [
                        'label' => Yii::t('app',"ABC Долг"),
                        'url' => ["/otchot/abc-debit", "year" => date('Y')],
                    ],
                    [
                        'label' => Yii::t('app',"Баланс клиентов"),
                        'url' => ["/otchot/receivables"],
                    ],
                    [
                        'label' => Yii::t('app',"Объем"),
                        'url' => ["/report/sale-weight", "year" => date('Y')],
                    ],
                    [
                        'label' => Yii::t('app',"Отчет клиента"),
                        'url' => ["/report/sale-by-more"],
                    ],
                    [
                        'label' => Yii::t('app',"По сотрудникам"),
                        'url' => ["/report/kpi"],
                    ],
                    [
                        'label' => Yii::t('app',"Ежедневный отчет"),
                        'url' => ["/report/daily-waybill"],
                    ],
                    [
                        'label' => Yii::t('app',"ОТК"),
                        'url' => ["/kpi-dc-fines/index"],
                    ],
                    [
                        'label' => Yii::t('app',"KPI МАНАЖЕР"),
                        'url' => ["/report/kpi-manager"],
                    ],
                ],
                'order' => 7
            ],
            [
                'label' => "<i class='metismenu-icon far fa-handshake'></i> <span class='name'>".Yii::t('app',"ФРАНЧАЙЗИНГ")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Настройки"),
                        'url' => ["/franchising/settings"]
                    ],
                    [
                        'label' => Yii::t('app',"Отчет"),
                        'url' => ["/franchising/act-index"]
                    ],
                    [
                        'label' => Yii::t('app',"Баланс"),
                        'url' => ["/franchising/balance"]
                    ],
                    [
                        'label' => Yii::t('app',"Статистика"),
                        'url' => ["/franchising/statistics"]
                    ],

                ],
                'order' => 8
            ],
            [
                'label' => "<i class='metismenu-icon far fa-chart-bar'></i> <span class='name'>".Yii::t('app',"Мониторинг")."</span>",
                'url' => ["/monitoring/waybills"],
                'order' => 9
            ],
            [
                'label' => "<i class='metismenu-icon fas fa-warehouse'></i> <span class='name'>".Yii::t('app',"ТМЦ ЦЕНТР. СКЛАД")."</span>",
                'items' => [
                                            // [
                                            //  'label' => "ПОСТУПЛЕНИЯ ТМЦ",
                                            //  'url' => ["/tms/create"]
                                            // ],
                    [
                        'label' => Yii::t('app',"ПЕРЕМЕЩЕНИЕ ТМЦ"),
                        'url' => ["/tms/transportation"]
                    ],
                    [
                        'label' => Yii::t('app',"ОСТАТОК ТМЦ"),
                        'url' => ["/tms/report2"]
                    ],
                ],
                'order' => 10
            ],
            [
                'label'=>"<i class='metismenu-icon fas fa-plus'></i> <span  class='name'>".Yii::t('app',"Новости")."</span> ",
                'url'=>['/newss'],
                'order'=>11
            ],
            [
                'label' => "<i class='metismenu-icon fa fa-cog'></i> <span class='name'>".Yii::t('app',"Настройки")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Сфера деятельности"),
                        'url' => ["/activity/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Город"),
                        'url' => ["/city/index"],
                    ],
                    [
                        'label' => Yii::t('app',"Офисы"),
                        'url' => ["/branch/index"],
                    ],
                    [
                        'label' => Yii::t('app',"Пользователи"),
                        'url' => ["/users/index"]
                    ],
                ],
                'order' => 12
            ],
                                    // [
                                    //  'label' => "<i class='metismenu-icon far fa-plus-square'></i> <span class='name'>Добавить KPI Курьера</span>",
                                    //  'url' => ["/employee-bonus/create"],
                                    //  'order' => 11
                                    // ],
        ];

        $franchisingMenu = [
            [
                'label' => "<i class='metismenu-icon far fa-handshake'></i> <span class='name'>".Yii::t('app',"ФРАНЧАЙЗИНГ")."</span>",
                'items' => [
                                            // [
                                            //     'label' => "Настройки",
                                            //     'url' => ["/franchising/settings"]
                                            // ],
                    [
                        'label' => Yii::t('app',"Отчет"),
                        'url' => ["/franchising/act-index"]
                    ],
                    [
                        'label' => Yii::t('app',"Баланс"),
                        'url' => ["/franchising/balance"]
                    ],
                    [
                        'label' => Yii::t('app',"Статистика"),
                        'url' => ["/franchising/statistics"]
                    ],
                ],
                'order' => 13
            ]
        ];

        $franchisingBossMenu = [
            [
                'label' => "<i class='metismenu-icon far fa-handshake'></i> <span class='name'>".Yii::t('app',"ФРАНЧАЙЗИНГ")."</span>",
                'items' => [
                    [
                        'label' => Yii::t('app',"Отчет"),
                        'url' => ["/franchising/act-index"]
                    ],
                    [
                        'label' => Yii::t('app',"Баланс"),
                        'url' => ["/franchising/balance"]
                    ],
                    [
                        'label' => Yii::t('app',"Статистика"),
                        'url' => ["/franchising/statistics"]
                    ],
                    [
                        'label' =>Yii::t('app',"Сотрудники"),
                        'url' => ["/employee/index"]
                    ],
                    [
                        'label' => Yii::t('app',"Пользователи"),
                        'url' => ["/users/index"]
                    ],

                ],

                'order' => 13
            ],
                                    // [
                                    //     'label' => "<i class='metismenu-icon fa fa-fax'></i> <span class='name'>KPI</span>",
                                    //     'url' => ["/report/kpi"],
                                    //     'order' => 14
                                    // ],
            [
                'label' => "<i class='metismenu-icon fa fa-th'></i> <span class='name'>Движениe почты</span>",
                'url' => ["/site/movement"],
                'order' => 8
            ],
        ]
        ?>
        <?php if ($identity->roleId == 4): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuCustomer); ?>
        <?php elseif ($identity->roleId == 2): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuAgent); ?>
        <?php elseif ($identity->roleId == 5): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuWarehouse); ?>
        <?php elseif ($identity->roleId == 10): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuDivision); ?>
        <?php elseif ($identity->roleId == 11): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuHumanResources); ?>
        <?php elseif ($identity->roleId == 12): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuHouseHould); ?>
        <?php elseif (in_array($identity->roleId, [90, 91])): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuCallDisp); ?>
        <?php elseif ($identity->roleId == 92): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuAccountant); ?>
        <?php elseif ($identity->roleId == 94): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuInternational); ?>
        <?php elseif ($identity->roleId == 95): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuSales); ?>
        <?php elseif ($identity->roleId == 96): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuCollection); ?>
        <?php elseif ($identity->roleId == 97): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuEcom); ?>
        <?php elseif ($identity->roleId == 98): ?>
            <?php $menuItems = array_merge($menuGeneral, $menuLeadCourier); ?>
        <?php elseif ($identity->roleId == 1) : ?>
            <?php $menuItems = array_merge($menuGeneral, $menuAdmin); ?>
        <?php elseif ($identity->roleId == 8) : ?>
            <?php $menuItems = array_merge($menuGeneral, $menuSuperAdmin); ?>
        <?php elseif ($identity->roleId == 100) : ?>
            <?php $menuItems = array_merge($menuGeneral, $franchisingBossMenu); ?>
        <?php endif; ?>

        <?php
        $menuArr = array_column($menuItems, 'order');
        array_multisort($menuArr, SORT_ASC, $menuItems);

        $nav = Nav::widget([
            'options' => ['class' => 'vertical-nav-menu metismenu'],
            'items' => $menuItems,
            'dropDownCaret' => '<i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>',
            'encodeLabels' => false,
            'activateParents' => true,
        ]);
        $nav = str_replace("vertical-nav-menu metismenu nav", "vertical-nav-menu metismenu", $nav);
        $nav = str_replace("<li class=\"dropdown\">", "<li>", $nav);
        $nav = str_replace("class=\"dropdown-toggle\"", "", $nav);
        $nav = str_replace("data-toggle=\"dropdown\"", "", $nav);
        $nav = str_replace("class=\"dropdown-menu\"", "", $nav);
        $nav = str_replace("<li class=\"active\"><a href=\"", "<li><a class=\"mm-active\" href=\"", $nav);
        $nav = str_replace("<li class=\"dropdown active\"><a", "<li class=\"mm-active\"><a class=\"mm-active\"", $nav);

        echo $nav;
        ?>
    </div>
</div>
</div>
<div class="app-main__outer">
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div>
                        <div class="page-title-head center-elem">
                            <span class="d-inline-block"><?= $this->title; ?></span>
                        </div>
                        <div class="page-title-subheading opacity-10">
                            <nav class="" aria-label="breadcrumb">
                                <?= Breadcrumbs::widget([
                                    'tag' => 'ol',
                                    'homeLink' => [
                                        'label' => '<i aria-hidden="true" class="fa fa-home"></i>',
                                        'url' => Yii::$app->homeUrl,
                                    ],
                                    'encodeLabels' => false,
                                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                    'itemTemplate' => "<li class=\"breadcrumb-item\">{link}</li>\n",
                                    'activeItemTemplate' => "<li class=\"active breadcrumb-item\">{link}</li>\n",
                                ]) ?>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($currentRoute == 'monitoring/home'): ?>
            <?= $content ?>
        <?php elseif ($currentRoute == 'site/update' or $currentRoute == 'site/i-create' or $currentRoute == 'tms/create' or $currentRoute == 'tms/update'): ?>
            <div class="container-site-update">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <?= Alert::widget([
                        'options' => [
                            'class' => 'show',
                        ],
                    ]) ?>
                    <?= $content ?>
                </div>
            </div>
        <?php endif ?>
    </div>
    <div class="app-wrapper-footer">
        <div class="app-footer">
            <div class="app-footer__inner">
                <div class="app-footer-left">
                    <div class="footer-dots">
                        <div class="dots-separator"></div>
                        <a class="dot-btn-wrapper dd-chart-btn-2"
                        href="<?= Url::to(['/dashboard/home']); ?>">
                        <i class="dot-btn-icon lnr-pie-chart icon-gradient bg-love-kiss"></i>
                    </a>
                    <div class="dots-separator"></div>
                    <div class="dropdown">
                        <a aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"
                        class="dot-btn-wrapper">
                        <i class="dot-btn-icon lnr-bullhorn icon-gradient bg-mean-fruit"></i>
                        <div class="badge badge-dot badge-abs badge-dot-sm badge-danger">
                        Notifications</div>
                    </a>
                    <div tabindex="-1" role="menu" aria-hidden="true"
                    class="dropdown-menu-xl rm-pointers dropdown-menu">
                    <div class="dropdown-menu-header mb-0">
                        <div class="dropdown-menu-header-inner bg-deep-blue">
                            <div class="menu-header-image opacity-1"
                            style="background-image: url('/themes/architectui/images/dropdown-header/city3.jpg');">
                        </div>
                        <div class="menu-header-content text-dark">
                            <h5 class="menu-header-title">Notifications</h5>
                            <h6 class="menu-header-subtitle">You have
                                <b>21</b> unread messages
                            </h6>
                        </div>
                    </div>
                </div>
                <ul
                class="tabs-animated-shadow tabs-animated nav nav-justified tabs-shadow-bordered p-3">
                <li class="nav-item">
                    <a role="tab" class="nav-link active" data-toggle="tab"
                    href="#tab-messages-header1">
                    <span>Messages</span>
                </a>
            </li>
            <li class="nav-item">
                <a role="tab" class="nav-link" data-toggle="tab"
                href="#tab-events-header1">
                <span>Events</span>
            </a>
        </li>
        <li class="nav-item">
            <a role="tab" class="nav-link" data-toggle="tab"
            href="#tab-errors-header1">
            <span>System Errors</span>
        </a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="tab-messages-header1" role="tabpanel">
        <div class="scroll-area-sm">
            <div class="scrollbar-container">
                <div class="p-3">
                    <div class="notifications-box">
                        <div
                        class="vertical-time-simple vertical-without-time vertical-timeline vertical-timeline--one-column">
                        <div
                        class="vertical-timeline-item dot-danger vertical-timeline-element">
                        <div>
                            <span
                            class="vertical-timeline-element-icon bounce-in"></span>
                            <div
                            class="vertical-timeline-element-content bounce-in">
                            <h4 class="timeline-title">All Hands
                            Meeting</h4>
                            <span
                            class="vertical-timeline-element-date"></span>
                        </div>
                    </div>
                </div>
                <div
                class="vertical-timeline-item dot-warning vertical-timeline-element">
                <div>
                    <span
                    class="vertical-timeline-element-icon bounce-in"></span>
                    <div
                    class="vertical-timeline-element-content bounce-in">
                    <p>
                        Yet another one, at
                        <span class="text-success">15:00
                        PM</span>
                    </p>
                    <span
                    class="vertical-timeline-element-date"></span>
                </div>
            </div>
        </div>
        <div
        class="vertical-timeline-item dot-success vertical-timeline-element">
        <div>
            <span
            class="vertical-timeline-element-icon bounce-in"></span>
            <div
            class="vertical-timeline-element-content bounce-in">
            <h4 class="timeline-title">
                Build the production release
                <span
                class="badge badge-danger ml-2">NEW</span>
            </h4>
            <span
            class="vertical-timeline-element-date"></span>
        </div>
    </div>
</div>
<div
class="vertical-timeline-item dot-primary vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <h4 class="timeline-title">
        Something not important
        <div
        class="avatar-wrapper mt-2 avatar-wrapper-overlap">
        <div
        class="avatar-icon-wrapper avatar-icon-sm">
        <div
        class="avatar-icon">
        <img src="/themes/architectui/images/avatars/1.jpg"
        alt="">
    </div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div
class="avatar-icon">
<img src="/themes/architectui/images/avatars/2.jpg"
alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div
class="avatar-icon">
<img src="/themes/architectui/images/avatars/3.jpg"
alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div
class="avatar-icon">
<img src="/themes/architectui/images/avatars/4.jpg"
alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div
class="avatar-icon">
<img src="/themes/architectui/images/avatars/5.jpg"
alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div
class="avatar-icon">
<img src="/themes/architectui/images/avatars/9.jpg"
alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div
class="avatar-icon">
<img src="/themes/architectui/images/avatars/7.jpg"
alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm">
<div
class="avatar-icon">
<img src="/themes/architectui/images/avatars/8.jpg"
alt="">
</div>
</div>
<div
class="avatar-icon-wrapper avatar-icon-sm avatar-icon-add">
<div
class="avatar-icon">
<i>+</i>
</div>
</div>
</div>
</h4>
<span
class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item dot-info vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <h4 class="timeline-title">This dot
    has an info state</h4>
    <span
    class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item dot-danger vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <h4 class="timeline-title">All Hands
    Meeting</h4>
    <span
    class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item dot-warning vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <p>
        Yet another one, at
        <span class="text-success">15:00
        PM</span>
    </p>
    <span
    class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item dot-success vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <h4 class="timeline-title">
        Build the production release
        <span
        class="badge badge-danger ml-2">NEW</span>
    </h4>
    <span
    class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item dot-dark vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in"></span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <h4 class="timeline-title">This dot
    has a dark state</h4>
    <span
    class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="tab-pane" id="tab-events-header1" role="tabpanel">
    <div class="scroll-area-sm">
        <div class="scrollbar-container">
            <div class="p-3">
                <div
                class="vertical-without-time vertical-timeline vertical-timeline--animate vertical-timeline--one-column">
                <div
                class="vertical-timeline-item vertical-timeline-element">
                <div>
                    <span
                    class="vertical-timeline-element-icon bounce-in">
                    <i
                    class="badge badge-dot badge-dot-xl badge-success"></i>
                </span>
                <div
                class="vertical-timeline-element-content bounce-in">
                <h4 class="timeline-title">All Hands
                Meeting</h4>
                <p>
                    Lorem ipsum dolor sic amet, today at
                    <a href="javascript:void(0);">12:00
                    PM</a>
                </p>
                <span
                class="vertical-timeline-element-date"></span>
            </div>
        </div>
    </div>
    <div
    class="vertical-timeline-item vertical-timeline-element">
    <div>
        <span
        class="vertical-timeline-element-icon bounce-in">
        <i
        class="badge badge-dot badge-dot-xl badge-warning"></i>
    </span>
    <div
    class="vertical-timeline-element-content bounce-in">
    <p>
        Another meeting today, at
        <b class="text-danger">12:00 PM</b>
    </p>
    <p>Yet another one, at
        <span class="text-success">15:00
        PM</span>
    </p>
    <span
    class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in">
    <i
    class="badge badge-dot badge-dot-xl badge-danger"></i>
</span>
<div
class="vertical-timeline-element-content bounce-in">
<h4 class="timeline-title">Build the
production release</h4>
<p>
    Lorem ipsum dolor sit
    amit,consectetur eiusmdd tempor
    incididunt ut labore et dolore magna
    elit enim at
    minim veniam quis nostrud
</p>
<span
class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in">
    <i
    class="badge badge-dot badge-dot-xl badge-primary"></i>
</span>
<div
class="vertical-timeline-element-content bounce-in">
<h4 class="timeline-title text-success">
Something not important</h4>
<p>
    Lorem ipsum dolor sit
    amit,consectetur elit enim at
    minim veniam quis nostrud
</p>
<span
class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in">
    <i
    class="badge badge-dot badge-dot-xl badge-success"></i>
</span>
<div
class="vertical-timeline-element-content bounce-in">
<h4 class="timeline-title">All Hands
Meeting</h4>
<p>
    Lorem ipsum dolor sic amet, today at
    <a href="javascript:void(0);">12:00
    PM</a>
</p>
<span
class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in">
    <i
    class="badge badge-dot badge-dot-xl badge-warning"></i>
</span>
<div
class="vertical-timeline-element-content bounce-in">
<p>
    Another meeting today, at
    <b class="text-danger">12:00 PM</b>
</p>
<p>Yet another one, at
    <span class="text-success">15:00
    PM</span>
</p>
<span
class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in">
    <i
    class="badge badge-dot badge-dot-xl badge-danger"></i>
</span>
<div
class="vertical-timeline-element-content bounce-in">
<h4 class="timeline-title">Build the
production release</h4>
<p>
    Lorem ipsum dolor sit
    amit,consectetur eiusmdd tempor
    incididunt ut labore et dolore magna
    elit enim at
    minim veniam quis nostrud
</p>
<span
class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
<div
class="vertical-timeline-item vertical-timeline-element">
<div>
    <span
    class="vertical-timeline-element-icon bounce-in">
    <i
    class="badge badge-dot badge-dot-xl badge-primary"></i>
</span>
<div
class="vertical-timeline-element-content bounce-in">
<h4 class="timeline-title text-success">
Something not important</h4>
<p>
    Lorem ipsum dolor sit
    amit,consectetur elit enim at
    minim veniam quis nostrud
</p>
<span
class="vertical-timeline-element-date"></span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="tab-pane" id="tab-errors-header1" role="tabpanel">
    <div class="scroll-area-sm">
        <div class="scrollbar-container">
            <div class="no-results pt-3 pb-0">
                <div
                class="swal2-icon swal2-success swal2-animate-success-icon">
                <div class="swal2-success-circular-line-left"
                style="background-color: rgb(255, 255, 255);">
            </div>
            <span class="swal2-success-line-tip"></span>
            <span class="swal2-success-line-long"></span>
            <div class="swal2-success-ring"></div>
            <div class="swal2-success-fix"
            style="background-color: rgb(255, 255, 255);">
        </div>
        <div class="swal2-success-circular-line-right"
        style="background-color: rgb(255, 255, 255);">
    </div>
</div>
<div class="results-subtitle">All caught up!</div>
<div class="results-title">There are no system errors!
</div>
</div>
</div>
</div>
</div>
</div>
<ul class="nav flex-column">
    <li class="nav-item-divider nav-item"></li>
    <li class="nav-item-btn text-center nav-item">
        <button
        class="btn-shadow btn-wide btn-pill btn btn-focus btn-sm">View
    Latest Changes</button>
</li>
</ul>
</div>
</div>
<div class="dots-separator"></div>
<div class="dropdown">
    <a class="dot-btn-wrapper" aria-haspopup="true" data-toggle="dropdown"
    aria-expanded="false">
    <i class="dot-btn-icon lnr-earth icon-gradient bg-happy-itmeo"></i>
</a>
<div tabindex="-1" role="menu" aria-hidden="true"
class="rm-pointers dropdown-menu">
<div class="dropdown-menu-header">
    <div class="dropdown-menu-header-inner pt-3 pb-4 bg-focus">
        <div class="menu-header-image opacity-05"
        style="background-image: url('/themes/architectui/images/dropdown-header/city2.jpg');">
    </div>
    <div class="menu-header-content text-center text-white">
        <h6 class="menu-header-subtitle mt-0">
            Tilni o'zgartirish<br>
            Изменить язык<br>
            Change language<br>
        </h6>
    </div>
</div>
</div>
<h6 tabindex="-1" class="dropdown-header text-center">
    TILLAR ЯЗЫКИ LANGUAGES
</h6>
<?php $lang = Yii::$app->language; ?>
<?= Html::a('<span class="mr-3 opacity-8 flag large UZ"></span> Oʻzbekcha', array_merge(
    \Yii::$app->request->get(),
    [\Yii::$app->controller->route, 'language' => 'uz']
),
[
    'class' => 'dropdown-item' . (($lang == 'uz') ? ' active' : ''),
]
); ?>
<?= Html::a('<span class="mr-3 opacity-8 flag large RU"></span> Русский', array_merge(
    \Yii::$app->request->get(),
    [\Yii::$app->controller->route, 'language' => 'ru']
),
[
    'class' => 'dropdown-item' . (($lang == 'ru') ? ' active' : ''),
]
); ?>
<?= Html::a('<span class="mr-3 opacity-8 flag large US"></span> English', array_merge(
    \Yii::$app->request->get(),
    [\Yii::$app->controller->route, 'language' => 'en']
),
[
    'class' => 'dropdown-item' . (($lang == 'en') ? ' active' : ''),
]
); ?>
</div>
</div>
<div class="dots-separator"></div>
</div>
</div>
<div class="app-footer-right"></div>
</div>
</div>
</div>
</div>
</div>
</div>
<?php $this->endBody() ?>
<?php $userId = 0; ?>
<?php if (!Yii::$app->user->isGuest): ?>
    <?php $userId = Yii::$app->user->id; ?>
<?php endif ?>
<script type="text/javascript">
    var errorSound;

    function apply_filter() {
        $('.grid-view').yiiGridView('applyFilter');
    }
    $('.uu').popover({
        trigger: 'focus',
        html: true,
    });

    function checkIsBlock() {
        var st = $('#waybill-senderdate').val();
        var pattern = /(\d{2})\.(\d{2})\.(\d{4})/;
        var dt = new Date(st.replace(pattern, '$3-$2-$1'));
        $.get('<?= url(['site/is-block']) ?>', {
            month: dt.getMonth() + 1
        }, function(data) {
            data = $.parseJSON(data);
            if (data.status == 'error') {
                // bootbox.alert('<div class="alert alert-danger" role="alert"><span class="fa fa-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ' + data.message + ' </div>');
                Swal.fire({
                    title: '<img src="/img/bts.png">',
                    html: '<h2 style="margin-top: 35px; margin-bottom: 20px; color: #f00; font-weight: bold;">ДИҚҚАТ !</h2><h4><b>' +
                    data.message + '</b></h4>',
                    width: 800,
                    type: 'warning',
                    scrollbarPadding: true,
                    animation: true,
                    position: 'center',
                })
            }
        });
    }

    $(function() {
        $('body .form-group').each(function() {
            let isChild = $(this).children().is('.control-label');
            if (!isChild) {
                $(this).addClass('padding-top-0');
            }
        });
        $('body').on('focus', '.form-group .form-control', function() {
            $(this).closest('.form-group').find('.control-label').addClass('control-label-top');
        }).on('focusout', '.form-group .form-control', function() {
            if ($(this).val() == '' || $(this).val() == '0') {
                $(this).removeClass('form-control-active');
                $('.waybill-postTypeOther').css('display', 'none');
                $(this).closest('.form-group').find('.control-label').removeClass('control-label-top');
            } else {
                $(this).addClass('form-control-active');
            }
        });
        $('body .form-group .form-control').each(function() {
            let elementParent = $(this).closest('.form-group');
            let count = 0;
            elementParent.find('.form-control').each(function() {
                if (!($(this).val() == '' || $(this).val() == '0' || $(this).val() ===
                    undefined || $(this).val() == null)) {
                    count++;
            }
        });
            if (count === 0) {
                $(this).removeClass('form-control-active');
                $(this).closest('.form-group').find('.control-label').removeClass('control-label-top');
            } else {
                $(this).addClass('form-control-active');
                $(this).closest('.form-group').find('.control-label').addClass('control-label-top');
            }
        });

        // var userId = <?= $userId ?>;
        // if (userId == 940) {
        //  $('.waybill-search-form').parent().parent().addClass("active");
        //  $('.waybill-search-form').find('input').focus();
        // }

        // $('.waybill-search-form').parent().parent().addClass("active");
        // $('.waybill-search-form').find('input').focus();

        $('.waybill-search-form').submit(function(e) {
            $(this).parent().parent().addClass("active");
            let inputVal = $(this).find('input').val();
            if (inputVal) {
                // console.log(inputVal);
            } else {
                $(this).find('input').focus();
                e.preventDefault();
            }
        });

        var $eventSelect = $(".iWSelect2");
        $eventSelect.select2();
        $eventSelect.on("select2:close", function(e) {
            var thisVal = 1 * $(this).val();
            if (thisVal > 0) {
                $(this).closest('.form-group').find('.control-label').addClass('control-label-top');
                $(this).closest('.form-group').find('.select2-selection__rendered').addClass(
                    'form-control-active');
            } else {
                $(this).closest('.form-group').find('.control-label').removeClass('control-label-top');
                $(this).closest('.form-group').find('.select2-selection__rendered').removeClass(
                    'form-control-active');
            }
        });

        errorSound = new Audio("/sounds/error.mp3");
        var soundsBarcode = "<?=$soundsBarcode?>";
        var urlAddress = "<?= $urlAddress ?>"
        if (soundsBarcode=="2" && urlAddress == "monitoring/home")
        {
            errorSound.play();
        }
    });
</script>
</body>

</html>
<?php $this->endPage() ?>
