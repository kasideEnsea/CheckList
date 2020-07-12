const status = document.getElementById("status");
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
    status.innerText = "Загрузка...";
    const url = document.location.origin + page;
    fetch("/view" + page)
        .then(value => value.text())
        .then(text => {
            status.innerText = "Загружено";
            content.innerHTML = text;
            const title = applyTitle();
            nodeScriptReplace(content);
            history.replaceState(page, title, url)
        }).catch(reason => {
        status.innerText = "Произошла ошибка при загрузке данных";
        console.error(reason);
    });
    return false;
}

//Нужны для выполнения скриптов в загруженном фрагменте
function nodeScriptReplace(node) {
    if (nodeScriptIs(node) === true) {
        node.parentNode.replaceChild(nodeScriptClone(node), node);
    } else {
        var i = 0;
        var children = node.childNodes;
        while (i < children.length) {
            nodeScriptReplace(children[i++]);
        }
    }

    return node;
}

function nodeScriptIs(node) {
    return node.tagName === 'SCRIPT';
}

function nodeScriptClone(node) {
    var script = document.createElement("script");
    script.text = node.innerHTML;
    for (var i = node.attributes.length - 1; i >= 0; i--) {
        script.setAttribute(node.attributes[i].name, node.attributes[i].value);
    }
    return script;
}

window.addEventListener('popstate', function() {
    loadView(document.location.pathname);
});

loadView(document.location.pathname);