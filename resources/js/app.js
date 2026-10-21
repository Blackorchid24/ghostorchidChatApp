import './bootstrap';
import '../css/app.css';

document.addEventListener('DOMContentLoaded', () => {
    const menu = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const scrim = document.getElementById('sidebarScrim');
    const closeMenu = () => {
        sidebar ?.classList.remove('open');
        scrim ?.classList.remove('show');
    };
    menu ?.addEventListener('click', () => {
        sidebar ?.classList.toggle('open');
        scrim ?.classList.toggle('show');
    });
    scrim ?.addEventListener('click', closeMenu);

    document.querySelectorAll('[data-password]').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.password);
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            button.innerHTML = input.type === 'password' ?
                '<i class="fa-regular fa-eye"></i>' :
                '<i class="fa-regular fa-eye-slash"></i>';
        });
    });

    const search = document.getElementById('conversationSearch');
    search ?.addEventListener('input', () => {
        const term = search.value.toLowerCase().trim();
        document.querySelectorAll('#conversationList .conversation-item').forEach(item => {
            item.style.display = item.dataset.search.includes(term) ? '' : 'none';
        });
    });

    const box = document.getElementById('messagesScroll');
    if (box) box.scrollTop = box.scrollHeight;

    const form = document.getElementById('messageForm');
    const input = document.getElementById('messageInput');
    if (form && input && window.ghostorchidChat) {
        input.addEventListener('keydown', event => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form.requestSubmit();
            }
        });

        form.addEventListener('submit', async event => {
            event.preventDefault();
            const body = input.value.trim();
            if (!body) return;
            const sendButton = form.querySelector('button[type="submit"]');
            sendButton.disabled = true;
            try {
                const response = await fetch(window.ghostorchidChat.sendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.ghostorchidChat.csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        body
                    })
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Message could not be sent.');
                appendMessage(data.message, true);
                input.value = '';
                box.scrollTop = box.scrollHeight;
            } catch (error) {
                alert(error.message || 'Unable to send message.');
            } finally {
                sendButton.disabled = false;
                input.focus();
            }
        });

        function appendMessage(message, own = false) {
            document.getElementById('emptyMessages') ?.remove();
            if (box.querySelector(`[data-message-id="${message.id}"]`)) return;
            const row = document.createElement('div');
            row.className = `message-row ${own ? 'mine' : ''}`;
            row.dataset.messageId = message.id;
            const avatar = document.createElement('img');
            avatar.className = 'message-avatar';
            avatar.src = message.user ?.avatar_url || '';
            avatar.alt = '';
            const content = document.createElement('div');
            content.className = 'message-content';
            const meta = document.createElement('div');
            meta.className = 'message-meta';
            const name = document.createElement('b');
            name.textContent = own ? 'You' : (message.user ?.name || 'Member');
            const time = document.createElement('time');
            time.textContent = new Date(message.created_at).toLocaleTimeString([], {
                hour: 'numeric',
                minute: '2-digit'
            });
            meta.append(name, time);
            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            bubble.textContent = message.body;
            content.append(meta, bubble);
            row.append(avatar, content);
            box.append(row);
        }

        if (window.Echo) {
            window.Echo.private(`conversation.${window.ghostorchidChat.conversationId}`)
                .listen('.message.sent', message => {
                    if (Number(message.user.id) !== Number(window.ghostorchidChat.currentUserId)) {
                        appendMessage(message, false);
                        box.scrollTop = box.scrollHeight;
                    }
                });
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('imageInput');
    const attachmentInput = document.getElementById('attachmentInput');
    const imageButton = document.getElementById('imageButton');
    const attachmentButton = document.getElementById('attachmentButton');
    const locationButton = document.getElementById('locationButton');
    const voiceButton = document.getElementById('voiceButton');
    const preview = document.getElementById('composerPreview');
    const imagePreview = document.getElementById('composerImagePreview');
    const fileName = document.getElementById('composerFileName');
    const fileIcon = document.getElementById('composerFileIcon');
    const removeFileButton = document.getElementById('removeComposerFile');
    const textarea = document.getElementById('messageInput');
    const status = document.getElementById('composerStatus');
    const recorderPanel = document.getElementById('voiceRecorder');
    const voiceStatus = document.getElementById('voiceStatus');
    const voiceTimer = document.getElementById('voiceTimer');
    const stopVoiceButton = document.getElementById('stopVoiceButton');
    const cancelVoiceButton = document.getElementById('cancelVoiceButton');
    const voicePreview = document.getElementById('voicePreview');

    if (!imageInput || !attachmentInput || !textarea) return;

    let selectedFile = null;
    let mediaRecorder = null;
    let audioChunks = [];
    let stream = null;
    let recordingStartedAt = 0;
    let timerInterval = null;
    let voiceUrl = null;

    function setStatus(message) {
        status.textContent = message;
    }

    function showFile(file, isImage) {
        selectedFile = file;
        preview.hidden = false;
        imagePreview.hidden = true;
        imagePreview.removeAttribute('src');
        fileName.textContent = file.name;
        fileIcon.className = isImage ?
            'fa-regular fa-image' :
            'fa-solid fa-file-lines';

        if (isImage) {
            const reader = new FileReader();

            reader.onload = function (event) {
                imagePreview.src = event.target.result;
                imagePreview.hidden = false;
            };

            reader.readAsDataURL(file);
        }

        setStatus('File selected. Media upload must be enabled on the server before it can be sent.');
    }

    imageButton.addEventListener('click', function () {
        imageInput.click();
    });

    attachmentButton.addEventListener('click', function () {
        attachmentInput.click();
    });

    imageInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            setStatus('Please select a valid image.');
            this.value = '';
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            setStatus('The image must be 10 MB or smaller.');
            this.value = '';
            return;
        }

        attachmentInput.value = '';
        showFile(file, true);
    });

    attachmentInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        if (file.size > 20 * 1024 * 1024) {
            setStatus('The attachment must be 20 MB or smaller.');
            this.value = '';
            return;
        }

        imageInput.value = '';
        showFile(file, false);
    });

    removeFileButton.addEventListener('click', function () {
        selectedFile = null;
        imageInput.value = '';
        attachmentInput.value = '';
        preview.hidden = true;
        imagePreview.hidden = true;
        imagePreview.removeAttribute('src');
        fileName.textContent = '';
        setStatus('Selected file removed.');
    });

    locationButton.addEventListener('click', function () {
        if (!navigator.geolocation) {
            setStatus('Location sharing is not supported by this browser.');
            return;
        }

        setStatus('Requesting your location permission...');

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const mapUrl = 'https://www.google.com/maps?q=' +
                    latitude + ',' + longitude;

                const locationMessage = '📍 My location: ' + mapUrl;
                const existingText = textarea.value.trim();

                textarea.value = existingText ?
                    existingText + '\n' + locationMessage :
                    locationMessage;

                textarea.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                textarea.focus();

                setStatus('Location link added to your message. Press Send to share it.');
            },
            function (error) {
                const messages = {
                    1: 'Location permission was denied.',
                    2: 'Your location is currently unavailable.',
                    3: 'Location request timed out.'
                };

                setStatus(messages[error.code] || 'Unable to get your location.');
            }, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    });

    function stopTracks() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }

    function clearRecordingTimer() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }

    function formatDuration(milliseconds) {
        const totalSeconds = Math.floor(milliseconds / 1000);
        const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
        const seconds = String(totalSeconds % 60).padStart(2, '0');
        return minutes + ':' + seconds;
    }

    function resetRecorder() {
        clearRecordingTimer();
        stopTracks();
        mediaRecorder = null;
        audioChunks = [];
        recorderPanel.hidden = true;
        voiceButton.disabled = false;
        voiceButton.innerHTML = '<i class="fa-solid fa-microphone"></i><span>Voice</span>';
    }

    voiceButton.addEventListener('click', async function () {
        if (!navigator.mediaDevices ?.getUserMedia || !window.MediaRecorder) {
            setStatus('Voice recording is not supported in this browser.');
            return;
        }

        try {
            if (voiceUrl) {
                URL.revokeObjectURL(voiceUrl);
                voiceUrl = null;
            }

            voicePreview.pause();
            voicePreview.removeAttribute('src');
            voicePreview.load();
            voicePreview.hidden = true;

            stream = await navigator.mediaDevices.getUserMedia({
                audio: true
            });

            const options = MediaRecorder.isTypeSupported('audio/webm;codecs=opus') ?
                {
                    mimeType: 'audio/webm;codecs=opus'
                } :
                {};

            mediaRecorder = new MediaRecorder(stream, options);
            audioChunks = [];
            recordingStartedAt = Date.now();

            mediaRecorder.addEventListener('dataavailable', function (event) {
                if (event.data.size > 0) audioChunks.push(event.data);
            });

            mediaRecorder.addEventListener('stop', function () {
                clearRecordingTimer();
                stopTracks();

                if (audioChunks.length) {
                    const audioBlob = new Blob(audioChunks, {
                        type: mediaRecorder ?.mimeType || 'audio/webm'
                    });

                    voiceUrl = URL.createObjectURL(audioBlob);
                    voicePreview.src = voiceUrl;
                    voicePreview.hidden = false;
                    voiceStatus.textContent = 'Recording ready to preview';
                    setStatus('Voice recording created. Sending audio requires server-side upload support.');
                }

                voiceButton.disabled = false;
                voiceButton.innerHTML = '<i class="fa-solid fa-microphone"></i><span>Voice</span>';
            });

            recorderPanel.hidden = false;
            voicePreview.hidden = true;
            voiceStatus.textContent = 'Recording...';
            voiceButton.disabled = true;
            voiceButton.innerHTML = '<i class="fa-solid fa-circle"></i><span>Recording</span>';

            mediaRecorder.start();

            timerInterval = setInterval(function () {
                voiceTimer.textContent = formatDuration(Date.now() - recordingStartedAt);
            }, 250);

            setStatus('Recording. Press Stop when you finish.');
        } catch (error) {
            stopTracks();
            setStatus('Microphone access failed. Allow microphone permission and try again.');
        }
    });

    stopVoiceButton.addEventListener('click', function () {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
        }
    });

    cancelVoiceButton.addEventListener('click', function () {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.addEventListener('stop', function () {
                audioChunks = [];
                resetRecorder();
                setStatus('Voice recording cancelled.');
            }, {
                once: true
            });

            mediaRecorder.stop();
        } else {
            audioChunks = [];
            resetRecorder();
            setStatus('Voice recording cancelled.');
        }

        voicePreview.pause();
        voicePreview.hidden = true;

        if (voiceUrl) {
            URL.revokeObjectURL(voiceUrl);
            voiceUrl = null;
        }
    });

    window.addEventListener('beforeunload', function () {
        clearRecordingTimer();
        stopTracks();

        if (voiceUrl) URL.revokeObjectURL(voiceUrl);
    });

    const form = document.getElementById('messageForm');

    form.addEventListener('submit', function (event) {
        if (selectedFile) {
            event.preventDefault();
            setStatus('Your text messages work normally, but image and file sending need backend upload support first.');
            return;
        }

        if (mediaRecorder && mediaRecorder.state === 'recording') {
            event.preventDefault();
            setStatus('Stop the voice recording before sending a message.');
        }
    });
});
