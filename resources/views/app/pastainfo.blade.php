<nav class="sidebar">
    @if (Route::currentRouteName() === 'pasta.show')
    <div class="side-item">
        <button
            class="button copy-link-button"
            type="button"
        >
        Copy link
        </button>
    </div>
    @endif
    <div class="side-item">
        @include('components.select', [
            'class' => 'language-select',
            'placeholder' => 'Language',
            'options' => $languages,
            'selected' => $pasta->language ?? null,
        ])
    </div>
    <div class="side-item">
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
    </div>
    <div class="side-item">
        <input
            class="input key-input"
            type="password"
            name="key"
            placeholder="Password (optional)"
            minlength="8"
            maxlength="255"
        >
    </div>
    <div class="side-item">
        <label>
            <input type="checkbox" value="" class="input-checkbox burn-on-read-input">
            Burn on read
        </label>
    </div>
    <div class="side-item">
        <button
            class="button save-button"
            type="button"
        >
        @if (!empty($pasta))
        Update
        @else
        Save
        @endif
        </button>
    </div>
    @if (!empty($pasta))
    <div class="pasta-details side-item">
        <h2>{{ __('Details') }}</h2>
        <span>Created: <strong>{{ $pasta->created_at }}</strong></span>
        <span>Expires: <strong>{{ $pasta->expires ?? 'Never' }}</strong></span>
        <span>Burn on read: <strong>{{ $pasta->burn_on_read ? 'Yes' : 'No' }}</strong></span>
        @if ($pasta->parent)
        <span>Parent: <a href="{{ route('pasta.show', $pasta->parent_id) }}"><strong>{{ $pasta->parent->uuid }}</strong></a></span>
        @endif
    </div>
    @endif
</nav>
