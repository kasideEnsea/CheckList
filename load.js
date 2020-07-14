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
function loadView(page) {
    const url = document.location.origin + page;
    fetch("/view" + page)
        .then(value => value.text())
        .then(text => {
            content.innerHTML = text;
            const title = applyTitle();
            nodeScriptReplace(content);
            processLocalLinks(content);
            updateHeader();
            history.replaceState(page, title, url)
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

window.addEventListener('popstate', function () {
    loadView(document.location.pathname);
});

processLocalLinks(document);
loadView(document.location.pathname);