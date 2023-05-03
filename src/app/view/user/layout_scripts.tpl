
{*jquery*}
{if $pjax==false}
    <script src="../../public/js/theme.js?v={$__version}" defer></script>
    <script src="../../public/js/jquery.min.js?v={$__version}" defer></script>
    {*pjax*}
    <script src="../../public/js/pjax.min.js?v={$__version}" defer></script>
    <!-- MDB ESSENTIAL -->

{/if}
<script src="../../public/app/index.js?v={$__version}" defer></script>
<script type="text/javascript" src="../../public/js/mdb.min.js?v={$__version}" defer></script>
<!-- MDB PLUGINS -->
<script type="text/javascript" src="../../public/plugins/js/file-upload.min.js?v={$__version}" defer></script>
{if $pjax==false}
    <script src="../../public/app/form.js?v={$__version}" defer></script>
    <script src="../../public/app/page.js?v={$__version}" defer></script>
    <script src="../../public/app/console.js?v={$__version}" defer></script>
{/if}
