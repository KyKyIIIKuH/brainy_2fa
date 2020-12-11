<script src="{$template_path}js/2fa.js"></script>
<h2>2FA</h2>
<div class="wrap">
    <div data-tab="web-server" style="display: block;">
        <h3>Управление</h3>
        <div class="list-div">
            <div>
                <a href="?do=2FA&subdo=createqr" class="btn btn-play">Создать новый QR code</a>
                
                {if $twofaqrcode != ''}
                    {if $status_2FA eq 'Отключить 2FA'}
                    	<a href="?do=2FA&subdo=dissable2fa" class="btn btn-stop">{$status_2FA}</a>
                    {else}
                        <a href="?do=2FA&subdo=enable2fa" class="btn btn-play">{$status_2FA}</a>
                    {/if}
                {/if}
                
            </div>
        </div>
        {if $twofaqrcode != ''}
        <hr>
        <h3>QR Code</h3>
        
        {foreach from=$twofaconf item=site}
            <p><a  href="{$twofaqrcode}" target="_blank"><img style="border: 0; padding:10px" src="{$twofaqrcode}"/></a></p>
            Ваш ключ для ручного ввода: {$site["secret"]}
        {/foreach}
        {/if}
    </div>
</div>