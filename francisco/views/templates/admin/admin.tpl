<div class="container">
    <div class="panel">
        <div class="panel-heading">
            {l s="Textos Personalizados" mod='francisco'}
        </div>
        <div class="panel-body">
            {if isset($textos) && $textos|count}
                <table class="table">
                    <thead>
                        <tr>
                            <th>{l s='ID' mod='francisco'}</th>
                            <th>{l s='Texto' mod='francisco'}</th>
                            <th>{l s='Acciones' mod='francisco'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $textos as $t}
                            <tr>
                                <td>{$t.id|intval}</td>
                                <td>{$t.text|escape:'html':'UTF-8'}</td>
                                <td>
                                    <a href="{$urls.edit}{$t.id|intval}" class="btn btn-default"><i class="icon-edit"></i> {l s='Editar' mod='francisco'}</a>
                                    <a href="{$urls.delete}{$t.id|intval}" class="btn btn-danger"><i class="icon-trash"></i> {l s='Eliminar' mod='francisco'}</a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            {else}
                <p class="alert alert-info">{l s='Aun no has creado ningun texto' mod='francisco'}</p>
            {/if}
        </div>
        <div class="panel-footer">
            <p class="text-right">
                <a href="{$urls.add|escape:'html':'UTF-8'}" class="btn btn-default"><i class="icon icon-plus"></i>{l s='Añadir Texto'}</a>
            </p>
        </div>
    </div>
</div>
<script>
    const url_ajax = "{$urls.ajax|escape:'html':'UTF-8'}";
    const msg_delete = "{l s='¿Estas seguro que quieres eliminarlo?' mod='francisco'}"
    const growl_error = "{l s='Error' mod='francisco'}"
    const growl_notice = "{l s='¡Correcto!' mod='francisco'}"
</script>
