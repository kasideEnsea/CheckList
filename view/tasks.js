function createTree(dataNode, DOMParent, level) {
    if (!dataNode)
        return;
    const ul = document.createElement("ul");
    DOMParent.appendChild(ul);
    for (let i = 0; i < dataNode.length; i++) {
        const value = dataNode[i];
        const li = document.createElement("li");
        ul.appendChild(li);
        const div = document.createElement("div");
        li.append(div);

        const textDiv = document.createElement("div");
        div.appendChild(textDiv);
        const description = document.createElement("span");
        textDiv.appendChild(description);
        description.innerText = value.description;

        const actions = document.createElement("div");
        div.appendChild(actions);
        addMenuListener(description, textDiv, actions, value.id)

        createTree(value.children, li, level + 1)
    }
}

function addMenuListener(node, textDiv, actions, id) {
    node.addEventListener("click", () => {
        if (textDiv.getElementsByTagName('input').length)
            return
        if (!actions.childElementCount)
            showActions(textDiv, actions, id)
        else
            actions.innerHTML = "";
    })
}

function processAddChild(node, id) {
    const pp = node.parentElement.parentElement;
    if (pp.lastElementChild.tagName !== "UL")
        pp.appendChild(document.createElement("ul"))
    const ul = pp.lastElementChild;
    if (ul.lastElementChild && ul.lastElementChild.getElementsByTagName("input").length)
        return;
    const li = document.createElement("li");
    ul.appendChild(li);
    addInput(li, "", value => showTasks(createTask(value, id)), () => {
        ul.removeChild(li);
    })
}

function showActions(textNode, node, id) {
    node.innerHTML = "";
    const editlink = document.createElement("span");
    node.appendChild(editlink);
    editlink.innerText = "Изменить ";
    editlink.classList.add("btn-link");
    editlink.addEventListener("click", () => processEdit(textNode, id))

    const removelink = document.createElement("span");
    node.appendChild(removelink);
    removelink.innerText = "Удалить ";
    removelink.classList.add("btn-link");
    removelink.addEventListener("click", () => showTasks(deleteTask(id)));

    const childlink = document.createElement("span");
    node.appendChild(childlink);
    childlink.innerText = "Подзадача ";
    childlink.classList.add("btn-link");
    childlink.addEventListener("click", () => processAddChild(node, id));
}

function addInput(node, value, callback, cancelCallback) {
    const descInput = document.createElement("input");
    node.appendChild(descInput);
    descInput.type = 'text';
    descInput.value = value;
    const submit = document.createElement("input");
    node.appendChild(submit);
    submit.type = 'submit';
    submit.value = 'Отправить';
    submit.addEventListener("click", () => {
        const cb = callback(descInput.value)
        if (cb) {
            cb.then(() => {
                descInput.value = '';
            });
        }
    });
    if (cancelCallback) {
        const cancel = document.createElement("input");
        node.appendChild(cancel);
        cancel.type = 'reset';
        cancel.value = 'Отмена';
        cancel.addEventListener("click", () => {
            cancelCallback(value);
        });
    }
}

function processEdit(editNode, id) {
    if (editNode.getElementsByTagName('input').length)
        return;
    const value = editNode.innerText;
    editNode.innerText = '';
    addInput(editNode, value, value => showTasks(editTask(id, value)), value => {
        editNode.innerHTML = "";
        const description = document.createElement("span");
        editNode.appendChild(description);
        description.innerText = value;
        const actions = editNode.parentElement.lastElementChild
        addMenuListener(description, editNode, actions, id);
    });
}

function createTask(value, parentId) {
    return process('post', {
        description: value,
        parent_id: parentId,
    });
}

function editTask(id, description) {
    return process('put', {
        id: id,
        description: description,
    });
}

function setTaskDone(id, done) {
    return process('put', {
        id: id,
        done: done
    });
}

function deleteTask(id) {
    return process('delete', {id: id});
}

function process(method, data) {
    data.comment = promptComment()
    if (data.comment === null)
        return;
    return fetch("/api/tasks.php", {
        method: method,
        body: JSON.stringify(data)
    });
}

function promptComment() {
    return prompt("Введите комментарий для публикации (необязательно):");
}

function showTasks(promise) {
    if (!promise)
        return;
    return promise.then(value => value.json())
        .then(value => {
            if (!value) {
                task.innerText = "Задач не найдено;"
                return;
            }
            task.innerHTML = '';
            createTree(value, task, 0);
        })
}

task = document.getElementById("task");
showTasks(fetch("/api/tasks.php"))
addInput(document.getElementById("task-form"), "", value => showTasks(createTask(value)));