<style>
    ::-webkit-scrollbar{
        width: 5px;
    }

    ::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 5px;
    }


    @media (prefers-color-scheme: dark) {
        body {
            background-color: #303030;
            color: #fff;
        }

    }


</style>

<!-- Google Fonts Roboto -->
<link rel="icon" href="../../public/img/default-monochrome.svg" type="image/x-icon"/>
<!-- Font Awesome -->
<link rel="preload" as="style" onload="this.rel='stylesheet'"
      href="../../public/css/fontawesome.min.css?v={$__version}"/>
<link
        rel="preload" as="style" onload="this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap"
/>


<!-- MDB ESSENTIAL -->
{if $theme == "dark"}
    <link rel="preload" as="style" onload="this.rel='stylesheet'" href="../../public/mdb/css/mdb.dark.min.css?v={$__version}"
          id="theme-link"/>
{else}
    <link rel="preload" as="style" onload="this.rel='stylesheet'" href="../../public/mdb/css/mdb.min.css?v={$__version}"
          id="theme-link"/>
{/if}
<link rel="stylesheet" href="../../public/css/mdbAdmin.css?v={$__version}"/>

