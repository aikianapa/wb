    <style>
        #printBody { width: 210mm; margin-left: auto; margin-right: auto; border: 1px #efefef solid; font-size: 11pt;}
        #printBody table.invoice_bank_rekv { border-collapse: collapse; border: 1px solid; }
        #printBody table.invoice_bank_rekv > tbody > tr > td, table.invoice_bank_rekv > tr > td { border: 1px solid; }
        #printBody table.invoice_items { border: 1px solid; border-collapse: collapse;}
        #printBody table.invoice_items td, table.invoice_items th { border: 1px solid;}
    </style>

<div id="printBody">
<table width="100%">
    <tr>
        <td>&nbsp;</td>
        <td style="width: 155mm;">
            <div style="width:155mm; ">Внимание! Оплата данного счета означает согласие с условиями поставки товара. Уведомление об оплате  обязательно, в противном случае не гарантируется наличие товара на складе. Товар отпускается по факту прихода денег на р/с Поставщика, самовывозом, при наличии доверенности и паспорта.</div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div style="text-align:center;  font-weight:bold;">
                Образец заполнения платежного поручения                                                                                                                                            </div>
        </td>
    </tr>
</table>


<table width="100%" cellpadding="2" cellspacing="2" class="invoice_bank_rekv"  role="formdata" data-wb-table="partners" data-wb-item="{{recipient}}">
    <tr>
        <td colspan="2" rowspan="2" style="min-height:13mm; width: 105mm;">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="height: 13mm;">
                <tr>
                    <td valign="top">
                        <div>{{bank}}</div>
                    </td>
                </tr>
                <tr>
                    <td valign="bottom" style="height: 3mm;">
                        <div style="font-size:10pt;">Банк получателя</div>
                    </td>
                </tr>
            </table>
        </td>
        <td style="min-height:7mm;height:auto; width: 25mm;">
            <div>БИK</div>
        </td>
        <td rowspan="2" style="vertical-align: top; width: 60mm;">
            <div style=" height: 7mm; line-height: 7mm; vertical-align: middle;">{{bik}}</div>
            <div>{{ks}}</div>
        </td>
    </tr>
    <tr>
        <td style="width: 25mm;">
            <div>Сч. №</div>
        </td>
    </tr>
    <tr>
        <td style="min-height:6mm; height:auto; width: 50mm;">
            <div>ИНН {{inn}}</div>
        </td>
        <td style="min-height:6mm; height:auto; width: 55mm;">
            <div>КПП {{kpp}}</div>
        </td>
        <td rowspan="2" style="min-height:19mm; height:auto; vertical-align: top; width: 25mm;">
            <div>Сч. №</div>
        </td>
        <td rowspan="2" style="min-height:19mm; height:auto; vertical-align: top; width: 60mm;">
            <div>{{account}}</div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="min-height:13mm; height:auto;">

            <table border="0" cellpadding="0" cellspacing="0" style="height: 13mm; width: 105mm;">
                <tr>
                    <td valign="top">
                        <div>{{name}}</div>
                    </td>
                </tr>
                <tr>
                    <td valign="bottom" style="height: 3mm;">
                        <div style="font-size: 10pt;">Получатель</div>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
<br/>

<div style="font-weight: bold; font-size: 16pt; padding-left:5px;">
    Счет № {{number}} от {{date}}</div>
<br/>

<div style="background-color:#000000; width:100%; font-size:1px; height:2px;">&nbsp;</div>

<table width="100%">
    <tr>
        <td style="width: 30mm;">
            <div style=" padding-left:2px;">Поставщик:	</div>
        </td>
        <td>
            <div style="font-weight:bold;  padding-left:2px;" role="formdata" data-wb-table="partners" data-wb-item="{{organisation}}">
                ИНН {{inn}}, {{name}}
            </div>
        </td>
    </tr>
    <tr>
        <td style="width: 30mm;">
            <div style=" padding-left:2px;">Покупатель:    </div>
        </td>
        <td>
            <div style="font-weight:bold;  padding-left:2px;" role="formdata" data-wb-table="partners" data-wb-item="{{partner}}">
                ИНН {{inn}}, {{name}}
            </div>
        </td>
    </tr>
</table>


<table class="invoice_items" width="100%" cellpadding="2" cellspacing="2">
    <thead>
    <tr>
        <th style="width:13mm;">№</th>
        <th style="width:20mm;">Код</th>
        <th>Товар</th>
        <th style="width:20mm;">Кол-во</th>
        <th style="width:17mm;">Ед.</th>
        <th style="width:27mm;">Цена</th>
        <th style="width:27mm;">Сумма</th>
    </tr>
    </thead>
    <tbody role="foreach" data-wb-from="products">
        <tr>
        <td style="widtd:13mm; text-align:center;">{{_ndx}}</td>
        <td style="widtd:20mm;" role="formdata" data-wb-table="products" data-wb-item="{{product}}">{{articul}}</td>
        <td data-role="formdata" role="formdata" data-wb-table="products" data-wb-item="{{product}}">{{name}}</td>
        <td style="widtd:20mm; text-align:center;">{{count}}</td>
        <td style="widtd:17mm; text-align:center;">Ед.</td>
        <td style="widtd:27mm; text-align:right;">{{price}}</td>
        <td style="widtd:27mm; text-align:right;">{{summ}}</td>
        </tr>
        <meta role="variable" var="lines" value="{{_ndx}}">
    </tbody>
</table>

<table border="0" width="100%" cellpadding="1" cellspacing="1">
    <tr>
        <td></td>
        <td style="width:27mm; font-weight:bold;  text-align:right;">Итого:</td>
        <td style="width:27mm; font-weight:bold;  text-align:right;">{{summ}}</td>
    </tr>
</table>

<br />
<div>
Всего наименований {{_VAR[lines]}} на сумму {{summ}} рублей.<br />
Ноль рублей 00 копеек</div>
<br /><br />
<div style="background-color:#000000; width:100%; font-size:1px; height:2px;">&nbsp;</div>
<br/>

<div role="formdata" data-wb-table="partners" data-wb-item="{{recipient}}">
<div>{{boss}} ______________________ ({{bossname}})</div>
<br/>

<div>{{buch}} ______________________ ({{buchname}})</div>
<br/>
</div>

<div style="width: 85mm;text-align:center;">М.П.</div>
<br/>


<div style="width:800px;text-align:left;font-size:10pt;">Счет действителен к оплате в течении трех дней.</div>
</div>
