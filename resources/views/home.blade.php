<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href={{asset('css/app.css')}}>
    <title>Short URL</title>
</head>
<body>
<div class="container">
    <header>
        <nav class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <a class="logo text-decoration-none" href="{{route('home')}}">Short URL</a>
            </div>
            <div class="menu mr-5">
                <span class="mr-5" id="user"></span>
                <a href="{{route('showLogin')}}" id="signIn" class="text-decoration-none">Sign In</a>
                <a href="{{route('showRegistration')}}" id="signUp" class="text-decoration-none">Sign Up</a>
                <a href="#" id="logout" class="text-decoration-none" style="display: none">Logout</a>
            </div>
        </nav>
    </header>

    <section class="convert_link d-flex flex-column justify-content-center align-items-center">
        <h1 class="pt-3 mb-4">Paste the URL to be shortened</h1>
        <form class="d-flex flex-column justify-content-center align-items-center">
            @csrf
            <label>Destination</label>
            <input class="mb-3" type="text" name="link" id="link" placeholder="Enter the link here ...">
            <label>Custom link (optional)</label>
            <input class="mb-3" type="text" name="short_code" id="customLink">
            <button type="button" class="btn btn-primary btn-lg" id="linkBtn">Shorten URL</button>
        </form>
        <div class="short_link mt-3" id="createdShortLink"></div>
    </section>
</div>

<section class="container" id="containerLinks" style="display: none">
    <hr>
    <h3>Links</h3>
    <div id="links"></div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous">
</script>

<script>
    let userSpan = document.getElementById('user');
    let signInLink = document.getElementById('signIn');
    let signUpLink = document.getElementById('signUp');
    let logoutLink = document.getElementById('logout');
    let linkInput = document.getElementById('link');
    let customLinkInput = document.getElementById('customLink');
    let linkButton = document.getElementById('linkBtn');
    let tokenInput = document.querySelector('input[name="_token"]');
    let createdShortLinkDiv = document.getElementById('createdShortLink');
    let containerLinksDiv = document.getElementById('containerLinks');
    let linksDiv = document.getElementById('links');

    function loadCurrentUserDetails() {
        let xhr = new XMLHttpRequest();
        xhr.open('GET', '/user');
        xhr.send();
        xhr.onload = function () {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.response);
                userSpan.innerText = response['firstName'] + ' ' + response['lastName'];
                signInLink.style.display = "none";
                signUpLink.style.display = "none";
                logoutLink.style.display = "inline";

                loadLinks();
            }
        }
    }

    loadCurrentUserDetails();

    linkButton.addEventListener('click', function () {
        let xhr = new XMLHttpRequest();
        xhr.open('POST', '/link');
        xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');

        let data = JSON.stringify(
            {
                'link': linkInput.value,
                'short_code': customLinkInput.value,
                '_token': tokenInput.value
            }
        );

        xhr.send(data);

        xhr.onload = function () {
            let response = JSON.parse(xhr.response);

            if (xhr.status === 200) {
                linkInput.value = '';
                customLinkInput.value = '';
                createdShortLinkDiv.style.display = 'inline-block';
                createdShortLinkDiv.innerText = response['short_link'];

                linksDiv.innerHTML = '<div class="d-flex flex-column mb-3 justify-content-between">' +
                    '        <div class="me-3">Original URL: <span id="originalLink" class="fst-italic">' + response['original_link'] + '</span></div>' +
                    '        <div class="me-3">Short URL: <span id="shortLink" class="fst-italic">' + response['short_link'] + '</span></div>' +
                    '        <div class="me-3">Redirected number: <span id="count" class="fst-italic">' + 0 + '</span></div>' +
                    '    </div>' + linksDiv.innerHTML;
            } else {
                alert(response['message']);
            }
        }
    });


    function loadLinks() {
        let xhr = new XMLHttpRequest();
        xhr.open('GET', '/links');
        xhr.send();
        xhr.onload = function () {
            let response = JSON.parse(xhr.response);

            containerLinksDiv.style.display = 'block';

            for (key in response) {
                let link = response[key];
                linksDiv.innerHTML += '<div class="d-flex flex-column mb-3 justify-content-between">' +
                    '        <div class="me-3">Original URL: <span id="originalLink" class="fst-italic">' + link['original_link'] + '</span></div>' +
                    '        <div class="me-3">Short URL: <span id="shortLink" class="fst-italic">' + link['short_link'] + '</span></div>' +
                    '        <div class="me-3">Redirected number: <span id="count" class="fst-italic">' + link['redirected_count'] + '</span></div>' +
                    '    </div>';
            }
        }
    }

    logoutLink.addEventListener('click', function () {
        let xhr = new XMLHttpRequest();
        xhr.open('GET', '/logout');
        xhr.send();
        xhr.onload = function () {
            signInLink.style.display = 'inline';
            signUpLink.style.display = 'inline';
            userSpan.style.display = 'none';
            logoutLink.style.display = 'none';
            containerLinksDiv.style.display = 'none';
        }
    });
</script>
</body>
</html>