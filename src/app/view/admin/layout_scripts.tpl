{if $pjax==false}
    <script type="text/javascript" src="../../public/js/theme.js"></script>
    <script type="text/javascript" src="../../public/js/jquery.min.js" defer></script>
    <script type="text/javascript" src="../../public/js/pjax.min.js" defer></script>
    <script type="text/javascript" src="../../public/mdb/js/mdb.min.js" defer></script>
    <script type="text/javascript" src="../../public/js/mdbAdmin.js" defer></script>
    <script type="text/javascript" src="../../public/app/main.js" defer></script>
{/if}

{if $pjax}
    {$__version = time()}
{/if}