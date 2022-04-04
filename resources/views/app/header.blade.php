<header>
    <nav>
        <div class="nav-left">
            <span class="nav-item nav-brand">
                <img src="/img/pasta.svg" alt="Pasta!" />
            </span>
        </div>
        <div class="nav-right">
            @if (Route::currentRouteName() === 'pasta.show')
            <span class="nav-item">
                <button
                    class="button copy-link-button"
                    type="button"
                >
                Copy link
                </button>
            </span>
            @endif
            <span class="nav-item">
                @include('components.select', [
                    'class' => 'language-select',
                    'placeholder' => 'Language',
                    'options' => $languages,
                    'selected' => $pasta->language ?? null,
                ])
            </span>
            <span class="nav-item">
                <input
                    class="input author-input"
                    type="text"
                    name="author"
                    placeholder="Author name"
                    @if (!empty($pasta->author))
                    value="{{ $pasta->author }}"
                    @else
                    value="Anonymous"
                    @endif
                    @if (Route::currentRouteName() === 'pasta.show')
                    readonly
                    @endif
                    minlength="1"
                    maxlength="255"
                >
            </span>
            <span class="nav-item">
                <input
                    class="input key-input"
                    type="password"
                    name="key"
                    placeholder="Password (optional)"
                    minlength="8"
                    maxlength="255"
                >
            </span>
            <span class="nav-item">
                <button
                    class="button save-button"
                    type="button"
                >
                @if (Route::currentRouteName() === 'pasta.show')
                Update
                @else
                Save
                @endif
                </button>
            </span>
        </div>
    </nav>
</header>
