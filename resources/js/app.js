import axios from 'axios';
import CodeMirror from 'codemirror';
import './codemirror-langs';

let languageSelector = document.querySelector('.language-select')

let cm = CodeMirror.fromTextArea(document.getElementById('pasta'), {
    theme: 'nord',
    mode: getChosenLanguage(),
    lineNumbers: true,
    indentUnit: 4,
    autofocus: true,
});

// Translate tabs to spaces
cm.setOption("extraKeys", {
    Tab: function(cm) {
        var spaces = Array(cm.getOption("indentUnit") + 1).join(" ");
        cm.replaceSelection(spaces);
    }
})

/**
 * Changing language
 */
function getChosenLanguage() {
    const language = languageSelector.value
    if (language === '' || language === null) {
        return 'php';
    }
    return language;
}

languageSelector.addEventListener('change', function () {
    cm.setOption('mode', getChosenLanguage())
})

/**
 * Saving
 */
let saveButton = document.querySelector('.save-button')
let author = document.querySelector('.author-input')
let key = document.querySelector('.key-input')
saveButton.addEventListener('click', async function () {
    if (!author.reportValidity() || !key.reportValidity()) {
        return false
    }

    const pasta = {
        content: cm.getDoc().getValue(),
        language: languageSelector.value,
        author: author.value,
        key: key.value,
    }

    let response = await axios.post('/p/', pasta);
    if (response.status === 201) {
        window.location = `/p/${response.data.uuid}/${key.value}`
    }
});

/**
 * Copy link
 */
let copyLinkButton = document.querySelector('.copy-link-button')
copyLinkButton.addEventListener('click', function () {
    navigator.clipboard.writeText(window.location.href)
    copyLinkButton.innerText = 'Copied!!!'
    setTimeout(function () {
        copyLinkButton.innerText = 'Copy link'
    }, 1500)


})

/**
 * About modal
 */
let info = document.querySelector('.info-trigger')
let overlay = document.querySelector('.modal-overlay')
info.addEventListener('click', function() {
    document.querySelector('body').classList.add('modal-open')
    document.querySelector('.modal-overlay').classList.add('modal-open')
    document.querySelector('.about-modal').classList.add('modal-open')
});

/**
 * Help modal
 */
let help = document.querySelector('.help-trigger')
help.addEventListener('click', function() {
    document.querySelector('body').classList.add('modal-open')
    document.querySelector('.modal-overlay').classList.add('modal-open')
    document.querySelector('.help-modal').classList.add('modal-open')
});

overlay.addEventListener('click', function() {
    document.querySelector('body').classList.remove('modal-open')
    document.querySelector('.modal-overlay').classList.remove('modal-open')
    document.querySelectorAll('.modal').forEach((el) => el.classList.remove('modal-open'))
});

/**
 * Help highlights
 */
let langHover = document.querySelector('.language-hover');
let langInput = document.querySelector('.language-select');
let authorHover = document.querySelector('.author-hover');
let authorInput = document.querySelector('.author-input');
let passHover = document.querySelector('.password-hover');
let passInput = document.querySelector('.key-input');

langHover.addEventListener('mouseover', function () {
    langInput.classList.add('highlight');
    setTimeout(
        function() {
            langInput.classList.remove('highlight')
        }, 1500
    );
})

authorHover.addEventListener('mouseover', function () {
    authorInput.classList.add('highlight');
    setTimeout(
        function() {
            authorInput.classList.remove('highlight')
        }, 1500
    );
})

passHover.addEventListener('mouseover', function () {
    passInput.classList.add('highlight');
    setTimeout(
        function() {
            passInput.classList.remove('highlight')
        }, 1500
    );
})
