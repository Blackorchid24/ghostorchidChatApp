@extends('layouts.app')

@section('title', 'Messages')
@section('heading', 'Your conversations')

@section('content')
    <div class="welcome-strip">
        <div>
            <span class="welcome-kicker">YOUR SPACE, AT YOUR PACE ✨</span>
            <h2>
                Good to see you, {{ explode(' ', auth()->user()->name)[0] }} 👋
                <span>✦</span>
            </h2>
            <p>💬 Find a conversation or start something new.</p>
        </div>

        <div class="welcome-orchid">
            <i class="fa-solid fa-spa"></i>
        </div>
    </div>

    @if (session('success'))
        <div class="conversation-notice success" role="status">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="conversation-notice error" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="mobile-chat-switcher" role="group" aria-label="Chat sections">
        <button type="button" class="mobile-section-button active" data-section="conversations"
            aria-controls="conversationPanel" aria-expanded="true">
            <i class="fa-solid fa-comments"></i>
            <span>Inbox</span>
            <span class="mobile-section-count">{{ $conversations->count() }}</span>
        </button>

        <button type="button" class="mobile-section-button" data-section="directory" aria-controls="directoryPanel"
            aria-expanded="false">
            <i class="fa-solid fa-user-plus"></i>
            <span>New Chat</span>
        </button>
    </div>

    <div class="chat-home-grid">

        <section class="panel conversation-panel mobile-chat-panel" id="conversationPanel"
            data-mobile-panel="conversations">

            <div class="panel-heading">
                <div>
                    <span class="section-kicker">YOUR INBOX 💌</span>
                    <h3>💬 Recent conversations</h3>
                </div>

                <span class="count-pill" title="Total conversations">
                    {{ $conversations->count() }}
                </span>
            </div>

            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input id="conversationSearch" type="search" placeholder="🔎 Search conversations..."
                    aria-label="Search conversations">
            </div>

            <div class="conversation-list" id="conversationList">
                @forelse ($conversations as $conversation)
                    @php
                        $other = $conversation->participants->firstWhere('id', '!=', auth()->id());

                        $isGroup = $conversation->type === 'group';

                        $title = $isGroup
                            ? ($conversation->name ?:
                            'Group conversation')
                            : $other?->name ?? 'Private conversation';

                        $preview = $conversation->latestMessage;

                        $previewText = $preview
                            ? ($preview->user_id === auth()->id()
                                    ? '😊 You: '
                                    : '💬 ' . ($preview->user?->name ?? 'Someone') . ': ') . $preview->body
                            : '👋 Say hello and start chatting!';

                        $searchText = strtolower($title . ' ' . ($preview?->body ?? ''));

                        $isCreator = $isGroup && (int) $conversation->created_by === (int) auth()->id();
                    @endphp

                    <div class="conversation-item-wrap">
                        <a class="conversation-item" data-search="{{ $searchText }}"
                            href="{{ route('chat.show', $conversation) }}">

                            <div class="conversation-avatar">
                                @if ($isGroup)
                                    <i class="fa-solid fa-user-group"></i>
                                @else
                                    <img src="{{ $other?->avatar_url ?? auth()->user()->avatar_url }}" alt=""
                                        loading="lazy">
                                @endif

                                @if (!$isGroup)
                                    <span
                                        class="status-dot {{ $other && $other->last_seen_at && $other->last_seen_at->gt(now()->subMinutes(2)) ? 'online' : '' }}"></span>
                                @endif
                            </div>

                            <div class="conversation-copy">
                                <div class="conversation-topline">
                                    <b>
                                        @if ($isGroup)
                                            👥
                                        @endif
                                        {{ $title }}
                                    </b>

                                    <small>
                                        {{ $preview?->created_at?->format('g:i A') ?? '' }}
                                    </small>
                                </div>

                                <p>{{ $previewText }}</p>
                            </div>
                        </a>

                        @if ($isCreator)
                            <div class="conversation-actions">
                                <details class="conversation-edit">
                                    <summary title="Edit group name" aria-label="Edit group name">
                                        <i class="fa-solid fa-pen"></i>
                                    </summary>

                                    <form method="POST" action="{{ route('chat.update', $conversation) }}"
                                        class="conversation-edit-form">

                                        @csrf
                                        @method('PATCH')

                                        <label for="group-name-{{ $conversation->id }}">
                                            ✏️ Edit group name
                                        </label>

                                        <input type="text" id="group-name-{{ $conversation->id }}" name="name"
                                            value="{{ $conversation->name }}" maxlength="100" required>

                                        <button type="submit" class="primary-button">
                                            Save changes
                                        </button>
                                    </form>
                                </details>
                            </div>
                        @endif
                    </div>

                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fa-regular fa-comments"></i>
                        </div>

                        <h3>📭 Your inbox is waiting!</h3>

                        <p>
                            💜 Start a private conversation with someone or
                            create a group to bring everyone together.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="panel directory-panel mobile-chat-panel" id="directoryPanel" data-mobile-panel="directory" hidden>

            <div class="panel-heading">
                <div>
                    <span class="section-kicker">MAKE A CONNECTION ✨</span>
                    <h3>💌 Start a conversation</h3>
                </div>

                <span class="panel-icon">
                    <i class="fa-solid fa-user-plus"></i>
                </span>
            </div>

            <p class="panel-description">
                Find someone for a private chat or bring your friends together
                in a group. 💜
            </p>

            <form class="new-chat-form" method="POST" action="{{ route('chat.private') }}">
                @csrf

                <label for="privateUserSearch">💬 Search someone to chat with</label>

                <input type="text" id="privateUserSearch" placeholder="Search by name..." autocomplete="off"
                    aria-controls="privateUserResults" aria-expanded="false" required>

                <input type="hidden" name="user_id" id="privateUser" required>

                <div id="privateUserResults" class="private-user-results" role="listbox" hidden>
                    @foreach ($users as $person)
                        <button type="button" class="private-user-result" role="option"
                            data-user-id="{{ $person->id }}" data-user-name="{{ $person->name }}"
                            aria-label="Select {{ $person->name }}">
                            <img class="private-user-avatar" src="{{ $person->avatar_url }}" alt=""
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">

                            <span class="private-user-name">{{ $person->name }}</span>
                            <i class="fa-solid fa-chevron-right private-user-arrow" aria-hidden="true"></i>
                        </button>
                    @endforeach
                </div>

                <div id="privateUserSelected" aria-live="polite"></div>

                <button class="primary-button full-button" type="submit">
                    💌 Start private chat
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('privateUserSearch');
                    const userIdInput = document.getElementById('privateUser');
                    const resultsContainer = document.getElementById('privateUserResults');
                    const selectedContainer = document.getElementById('privateUserSelected');
                    const form = searchInput.closest('form');

                    const users = Array.from(
                        resultsContainer.querySelectorAll('.private-user-result')
                    );

                    function filterUsers() {
                        const query = searchInput.value.trim().toLowerCase();
                        let matches = 0;

                        users.forEach(function(user) {
                            const name = user.dataset.userName.toLowerCase();
                            const match = query !== '' && name.includes(query);

                            user.hidden = !match;

                            if (match) {
                                matches++;
                            }
                        });

                        resultsContainer.hidden = query === '' || matches === 0;

                        searchInput.setAttribute(
                            'aria-expanded',
                            String(!resultsContainer.hidden)
                        );

                        if (query && matches === 0) {
                            selectedContainer.textContent = 'No matching names found.';
                        } else if (!userIdInput.value) {
                            selectedContainer.textContent = '';
                        }
                    }

                    searchInput.addEventListener('input', function() {
                        userIdInput.value = '';
                        selectedContainer.textContent = '';
                        filterUsers();
                    });

                    users.forEach(function(user) {
                        user.addEventListener('click', function() {
                            const userName = user.dataset.userName;

                            userIdInput.value = user.dataset.userId;
                            searchInput.value = userName;

                            selectedContainer.textContent = 'Selected: ' + userName;
                            selectedContainer.classList.add('user-selected');

                            resultsContainer.hidden = true;
                            searchInput.setAttribute('aria-expanded', 'false');

                            users.forEach(function(item) {
                                const isSelected = item === user;

                                item.setAttribute('aria-selected', String(isSelected));
                                item.classList.toggle('is-selected', isSelected);
                            });
                        });
                    });

                    form.addEventListener('submit', function(event) {
                        if (!userIdInput.value) {
                            event.preventDefault();

                            selectedContainer.textContent =
                                'Please search for and select a person first.';

                            searchInput.focus();
                        }
                    });

                    document.addEventListener('click', function(event) {
                        if (!form.contains(event.target)) {
                            resultsContainer.hidden = true;
                            searchInput.setAttribute('aria-expanded', 'false');
                        }
                    });
                });
            </script>

            <div class="thin-divider"></div>

            <form class="new-chat-form" method="POST" action="{{ route('chat.group') }}">
                @csrf

                <label for="groupName">👥 Group name</label>

                <input type="text" id="groupName" name="name" placeholder="e.g. 🎉 Weekend plans" maxlength="100"
                    required>

                <label>
                    🫂 Invite members
                    <span class="muted">(optional)</span>
                </label>

                <div class="member-picker">
                    @forelse ($users as $person)
                        <label class="member-option">
                            <input type="checkbox" name="members[]" value="{{ $person->id }}">

                            <img src="{{ $person->avatar_url ?? asset('images/default-avatar.png') }}" alt=""
                                loading="lazy">

                            <span>{{ $person->name }}</span>
                        </label>
                    @empty
                        <p class="muted">
                            No other members are available to invite yet.
                        </p>
                    @endforelse
                </div>

                <button class="secondary-button full-button" type="submit">
                    <i class="fa-solid fa-users"></i>
                    Create group 🎉
                </button>
            </form>
        </section>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('conversationSearch');
            const conversationList = document.getElementById('conversationList');

            if (searchInput && conversationList) {
                const wrappers = conversationList.querySelectorAll(
                    '.conversation-item-wrap'
                );

                searchInput.addEventListener('input', function() {
                    const query = this.value.trim().toLowerCase();

                    wrappers.forEach(function(wrapper) {
                        const conversation = wrapper.querySelector(
                            '.conversation-item'
                        );

                        const searchText = (
                            conversation?.dataset.search ||
                            conversation?.textContent ||
                            ''
                        ).toLowerCase();

                        wrapper.hidden = !searchText.includes(query);
                    });
                });
            }

            const switcher = document.querySelector('.mobile-chat-switcher');

            if (!switcher) {
                return;
            }

            const buttons = Array.from(
                switcher.querySelectorAll('[data-section]')
            );

            const panels = Array.from(
                document.querySelectorAll('[data-mobile-panel]')
            );

            const mobileQuery = window.matchMedia('(max-width: 768px)');
            let activeSection = 'conversations';

            function renderSections() {
                buttons.forEach(function(button) {
                    const active = button.dataset.section === activeSection;

                    button.classList.toggle('active', active);
                    button.setAttribute('aria-expanded', String(active));
                });

                panels.forEach(function(panel) {
                    const active = panel.dataset.mobilePanel === activeSection;

                    panel.hidden = mobileQuery.matches && !active;
                    panel.classList.toggle('is-mobile-active', active);
                });
            }

            buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                    activeSection = button.dataset.section;
                    renderSections();
                });
            });

            if (mobileQuery.addEventListener) {
                mobileQuery.addEventListener('change', renderSections);
            } else {
                mobileQuery.addListener(renderSections);
            }

            renderSections();
        });
    </script>
@endsection
