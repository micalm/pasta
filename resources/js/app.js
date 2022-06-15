import axios from 'axios';
import CodeMirror from 'codemirror';
import './codemirror-langs';
import flatpickr from "flatpickr";

let languageSelector = document.querySelector('.language-select')
let expirySelector = document.querySelector('.expires-input')

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
 * Flatpickr for expiry datetime picking
 */
flatpickr(expirySelector, {
    enableTime: true,
    dateFormat: "Z",
    altInput: true,
    altFormat: "Y-m-d H:i",
    minDate: new Date(),
})

/**
 * Saving
 */
let saveButton = document.querySelector('.save-button')
let author = document.querySelector('.author-input')
let key = document.querySelector('.key-input')
let parentId = document.querySelector('.parentid-input')
let burnOnRead = document.querySelector('.burn-on-read-input');
let expiresAt = document.querySelector('.expires-input');

saveButton.addEventListener('click', async function () {
    if (!author.reportValidity() || !key.reportValidity()) {
        return false
    }

    const pasta = {
        content: cm.getDoc().getValue(),
        parent_id: parentId ? parentId.value : null,
        language: languageSelector.value,
        author: author.value,
        key: key.value,
        burn_on_read: burnOnRead.checked,
        expires_at: expiresAt.value,
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
if (copyLinkButton !== null) {
    copyLinkButton.addEventListener('click', function () {
        navigator.clipboard.writeText(window.location.href)
        copyLinkButton.innerText = 'Copied!!!'
        setTimeout(function () {
            copyLinkButton.innerText = 'Copy link'
        }, 1500)
    })
}

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
    console.log('HELP');
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
