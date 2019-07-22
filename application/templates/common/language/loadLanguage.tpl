{if $smarty.session.user.lang eq 'ru'}

    {if isset($controller)}
        {config_load file=$cnf.rulang.path section=$controller scope=global}
        {if isset($section)}
            {config_load file=$cnf.rulang.path section=$section scope=global}
        {/if}
    {/if}

{elseif $smarty.session.user.lang eq 'en' || $smarty.session.user.lang neq 'ru'}

    {if isset($controller)}
        {config_load file=$cnf.englang.path section=$controller scope=global}
        {if isset($section)}
            {config_load file=$cnf.englang.path section=$section scope=global}
        {/if}
    {/if}

{/if}