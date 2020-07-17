const content = document.getElementById("content");

//Заменяет текущий заголовок на новый из загруженного фразмента
function applyTitle() {
    const title =
        content.getElementsByTagName("title")[0] ||
        content.getElementsByTagName("h1")[0];
    if (title) {
        return document.title = title.innerText;
    }
}

//Загрузка и подстановка загруженных страниц
function loadView(page, data) {
    const url = document.location.origin + page;
    fetch("/view" + page, data ? {
        body: data,
        method: 'post'
    } : undefined)
        .then(value => value.text())
        .then(text => {
            content.innerHTML = text;
            const title = applyTitle();
            history.replaceState(page, title, url)
            nodeScriptReplace(content);
            processLocalLinks(content);
            processLocalForms(content);
            updateHeader();
        }).catch(reason => {
        console.error(reason);
    });
    return false;
}

//Нужны для выполнения скриптов в загруженном фрагменте
function nodeScriptReplace(node) {
    if (node.tagName === 'SCRIPT') {
        node.parentNode.replaceChild(nodeScriptClone(node), node);
    } else {
        const children = node.childNodes;
        for (let i = 0; i < children.length; i++) {
            nodeScriptReplace(children[i]);
        }
    }
    return node;
}

function nodeScriptClone(node) {
    const script = document.createElement("script");
    script.text = node.innerHTML;
    for (let i = 0; i < node.attributes.length; i++) {
        script.setAttribute(node.attributes[i].name, node.attributes[i].value);
    }
    return script;
}

function processLocalLinks(node) {
    const links = node.getElementsByTagName("a");
    for (let i = 0; i < links.length; i++) {
        if (links[i].origin === document.location.origin) {
            links[i].addEventListener("click", ev => {
                ev.preventDefault();
                loadView(links[i].pathname + links[i].search);
            });
        }
    }
}

function processLocalForms(node) {
    const forms = node.getElementsByTagName("form");
    for (let i = 0; i < forms.length; i++) {
        const form = forms[i];
        const url = new URL(form.action);
        if (url.origin === document.location.origin) {
            form.addEventListener("submit", ev => {
                ev.preventDefault();
                let data;
                if(form.encoding === "application/x-www-form-urlencoded") {
                    data = new URLSearchParams();
                    for (const pair of new FormData(form)) {
                        data.append(pair[0], pair[1].toString());
                    }
                } else {
                    data = new FormData(form);
                }
                loadView(url.pathname + url.search, data);
            });
        }
    }
}

window.addEventListener('popstate', function () {
    loadView(document.location.pathname + document.location.search);
});

processLocalLinks(document);
loadView(document.location.pathname + document.location.search);
