

{if isset($textos) && $textos|count}
    {foreach $textos as $t}
        <li>{$t.text|escape:'html':'UTF-8'}</li>
    {/foreach}
{/if}
