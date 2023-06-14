<style>
    ::-webkit-scrollbar {
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
    .card{
        --mdb-card-bg: #ffffffd9!important;
    }

    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgb(59, 59, 59);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 20px;
    }

    .loader {
        width: 60px;
        height: 60px;
        border: 6px solid #ffffff;
        border-top-color: #3498db;
        border-radius: 50%;
        animation: spin 1.5s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

</style>

<!-- Google Fonts Roboto -->

<link rel="stylesheet" href="../../public/css/font.css?v={$__version}" media="none" onload="this.media='all'"/>
<!-- MDB ESSENTIAL -->
{if $theme == "dark"}
    <link id="theme-link" rel="preload" as="style" onload="this.rel='stylesheet'"
          href="../../public/css/mdb.dark.min.css?v={$__version}"/>
{else}
    <link id="theme-link" rel="preload" as="style" onload="this.rel='stylesheet'"
          href="../../public/css/mdb.min.css?v={$__version}"/>
{/if}
<!-- Font Awesome -->
<link rel="stylesheet" href="../../public/css/all.min.css?v={$__version}" media="none" onload="this.media='all'"/>

