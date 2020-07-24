<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<!-- Include the plugin's CSS and JS: -->
<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\Modal;

?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <style type="text/css">
        body{
            padding-top:85px;
            font-family: Trebuchet MS, Lucida Sans Unicode, Arial, sans-serif;
            line-height:130%;
        }

        .selectBoxArrow{
            margin-top: -30px;
            margin-right: 18px;
            position:absolute;
            right:1px;
        }
        .selectBoxInput{
            padding: 6px 12px;
            height: 35px;
        }
        .selectBox{
/*            border:1px solid #7f9db9;
*/
            height:20px;
        }
        .selectBoxOptionContainer{
            position:absolute;
            border:1px solid #7f9db9;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            height:60px;
            background-color:#FFF;
            left:-1px;
            top:20px;
            visibility:hidden;
            overflow:auto;
            margin-top: 36px;
            margin-left:17px;
        }
        .selectBoxAnOption{
            font-family:arial;
            font-size:12px;
            cursor:default;
            margin:1px;
            overflow:hidden;
            white-space:nowrap;
        }
        .selectBoxIframe{
            position:absolute;
            background-color:#FFF;
            border:0px;
            z-index:999;
        }
    </style>
    <script type="text/javascript">
        /************************************************************************************************************
         Editable select
         Copyright (C) September 2005  DHTMLGoodies.com, Alf Magne Kalleland

         This library is free software; you can redistribute it and/or
         modify it under the terms of the GNU Lesser General Public
         License as published by the Free Software Foundation; either
         version 2.1 of the License, or (at your option) any later version.

         This library is distributed in the hope that it will be useful,
         but WITHOUT ANY WARRANTY; without even the implied warranty of
         MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
         Lesser General Public License for more details.

         You should have received a copy of the GNU Lesser General Public
         License along with this library; if not, write to the Free Software
         Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

         Dhtmlgoodies.com., hereby disclaims all copyright interest in this script
         written by Alf Magne Kalleland.

         Alf Magne Kalleland, 2006
         Owner of DHTMLgoodies.com

         ************************************************************************************************************/

            // Path to arrow images
//        var arrowImage = '../web/images/select_arrow.gif';	// Regular arrow
        var arrowImage = '../web/images/arrowdown.png';	// Regular arrow
//        var arrowImageOver = '../web/images/select_arrow_over.gif';	// Mouse over
        var arrowImageOver = '../web/images/arrowdown.png';	// Mouse over
//        var arrowImageDown = '../web/images/select_arrow_down.gif';	// Mouse down
        var arrowImageDown = '../web/images/arrowdownselect.png';	// Mouse down


        var selectBoxIds = 0;
        var currentlyOpenedOptionBox = false;
        var editableSelect_activeArrow = false;



        function selectBox_switchImageUrl()
        {
            if(this.src.indexOf(arrowImage)>=0){
                this.src = this.src.replace(arrowImage,arrowImageOver);
            }else{
                this.src = this.src.replace(arrowImageOver,arrowImage);
            }


        }

        function selectBox_showOptions()
        {
            if(editableSelect_activeArrow && editableSelect_activeArrow!=this){
                editableSelect_activeArrow.src = arrowImage;

            }
            editableSelect_activeArrow = this;

            var numId = this.id.replace(/[^\d]/g,'');
            var optionDiv = document.getElementById('selectBoxOptions' + numId);
            if(optionDiv.style.display=='block'){
                optionDiv.style.display='none';
                if(navigator.userAgent.indexOf('MSIE')>=0)document.getElementById('selectBoxIframe' + numId).style.display='none';
                this.src = arrowImageOver;
            }else{
                optionDiv.style.display='block';
                if(navigator.userAgent.indexOf('MSIE')>=0)document.getElementById('selectBoxIframe' + numId).style.display='block';
                this.src = arrowImageDown;
                if(currentlyOpenedOptionBox && currentlyOpenedOptionBox!=optionDiv)currentlyOpenedOptionBox.style.display='none';
                currentlyOpenedOptionBox= optionDiv;
            }
//            document.onclick=function() { if (currentlyOpenedOptionBox.style.display == 'active') currentlyOpenedOptionBox.style.display = 'none';}
        }

        function selectOptionValue()
        {
            var parentNode = this.parentNode.parentNode;
            var textInput = parentNode.getElementsByTagName('INPUT')[0];
            $texto = this.innerHTML;
            $texto = $texto.split(": ");
            textInput.value = $texto[$texto.length-1];
            if(textInput.id === "gp") {
                $.post("index.php?r=order/compartment&gp=" + ($texto) + "&kg=" + $("#order-orderkg").val() + "&idc=" + $("#order-compartment_idcompartment").val() + "&numc=" + $("#order-compartment_idcompartment option:selected").text(),
                    function (data) {
                        $("#order-compartment_idcompartment").html(data);
                    });
                var el = $("input#gp").get(0);
                var elemLen = el.value.length;
                el.selectionStart = elemLen;
                el.selectionEnd = elemLen;
                el.focus();
                this.parentNode.style.display = 'none';
                document.getElementById('arrowSelectBox' + parentNode.id.replace(/[^\d]/g, '')).src = arrowImageOver;
            }
            if(navigator.userAgent.indexOf('MSIE')>=0)document.getElementById('selectBoxIframe' + parentNode.id.replace(/[^\d]/g,'')).style.display='none';
        }
        var activeOption;
        function highlightSelectBoxOption()
        {
            if(this.style.backgroundColor=='#316AC5'){
                this.style.backgroundColor='';
                this.style.color='';
            }else{
                this.style.backgroundColor='#316AC5';
                this.style.color='#FFF';
            }

            if(activeOption){
                activeOption.style.backgroundColor='';
                activeOption.style.color='';
            }
            activeOption = this;

        }
        var arrow = 0;
        function createEditableSelect(dest)
        {

            dest.className= dest.className+' selectBoxInput';
            var div = document.createElement('DIV');
            div.style.styleFloat = 'right';
            div.style.width = dest.offsetWidth + 'px';
//            div.style.position = 'relative';
            div.id = 'selectBox' + selectBoxIds;
            var parent = dest.parentNode;
            parent.insertBefore(div,dest);
            div.appendChild(dest);
            div.className='selectBox';
            div.style.zIndex = 10000 - selectBoxIds;
                var img = document.createElement('IMG');
                img.src = arrowImage;
                img.className = 'selectBoxArrow';

                img.onmouseover = selectBox_switchImageUrl;
                img.onmouseout = selectBox_switchImageUrl;
                img.onclick = selectBox_showOptions;
                img.id = 'arrowSelectBox' + selectBoxIds;
                $("*").click(function(){
                    if(editableSelect_activeArrow && editableSelect_activeArrow!=this){
                        editableSelect_activeArrow.src = arrowImage;

                    }
                    editableSelect_activeArrow = this;

                    var numId = this.id.replace(/[^\d]/g,'');
                    var optionDiv = document.getElementById('selectBoxOptions' + numId);
                });
                div.appendChild(img);
            var optionDiv = document.createElement('DIV');
            optionDiv.id = 'selectBoxOptions' + selectBoxIds;
            optionDiv.className='selectBoxOptionContainer';
            optionDiv.style.width = div.offsetWidth-2 + 'px';
            div.appendChild(optionDiv);

            if(navigator.userAgent.indexOf('MSIE')>=0){
                var iframe = document.createElement('<IFRAME src="about:blank" frameborder=0>');
                iframe.style.width = optionDiv.style.width;
                iframe.style.height = optionDiv.offsetHeight + 'px';
                iframe.style.display='none';
                iframe.id = 'selectBoxIframe' + selectBoxIds;
                div.appendChild(iframe);
            }

            if(dest.getAttribute('selectBoxOptions')){
                var options = dest.getAttribute('selectBoxOptions').split(';');
                var optionsTotalHeight = 0;
                var optionArray = new Array();
                for(var no=0;no<options.length;no++){
                    var anOption = document.createElement('DIV');
                    anOption.innerHTML = options[no];
                    anOption.className='selectBoxAnOption';
                    anOption.onclick = selectOptionValue;
                    anOption.style.width = optionDiv.style.width.replace('px','') - 2 + 'px';
                    anOption.onmouseover = highlightSelectBoxOption;
                    optionDiv.appendChild(anOption);
                    optionsTotalHeight = optionsTotalHeight + anOption.offsetHeight;
                    optionArray.push(anOption);
                }
                if(optionsTotalHeight > optionDiv.offsetHeight){
                    for(var no=0;no<optionArray.length;no++){
                        optionArray[no].style.width = optionDiv.style.width.replace('px','') - 22 + 'px';
                    }
                }
                optionDiv.style.display='none';
                optionDiv.style.visibility='visible';
            }

            selectBoxIds = selectBoxIds + 1;
            if($("#arrowSelectBox".concat(selectBoxIds-1))){
                $("#arrowSelectBox".concat(selectBoxIds-2)).attr("hidden", "true");
            }

        }

    </script>

    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>Systema MOPS</title>
    <?php $this->head() ?>
</head>
<body>

<?php
Modal::begin([
    'id' => 'edit',
    'size' => 'modal-lg',
]);

echo "<div id='editContent'></div>";

Modal::end();
?>
<?php $this->beginBody() ?>

    <?php
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            Html::beginForm(['/site/logout'], 'post')
            . Html::endForm()
            ],
    ]);
    ?>

        <?= $content ?>


<?php  echo $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
