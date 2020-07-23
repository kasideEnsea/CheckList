<div class="text-center">
    <h1>Добро пожаловать на главную страницу</h1>
    <img class="mx-auto" src="/images/corgi.jpg" alt="corgi"/>
    <h1 id="status-header"></h1>
    <script>
        sh = document.getElementById("status-header");
        if (!isAuthorized())
            sh.innerText = "Вам необходимо авторизоваться для продолжения";
        else
            sh.innerText = "Ура, вы авторизованы!!!!";
    </script>
</div>