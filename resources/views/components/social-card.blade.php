<div class="fixed bottom-6 right-6 z-50">
    <div class="card-social">
        <a href="{{ $settings['social_facebook'] ?? 'https://www.facebook.com/dribblingbd' }}" target="_blank" rel="noopener noreferrer" class="socialContainer containerOne" aria-label="Facebook">
            <i class="fab fa-facebook-f socialIcon"></i>
        </a>

        <a href="{{ $settings['social_instagram'] ?? 'https://www.instagram.com/dribbling_bd1' }}" target="_blank" rel="noopener noreferrer" class="socialContainer containerTwo" aria-label="Instagram">
            <i class="fab fa-instagram socialIcon"></i>
        </a>

        <a href="{{ $settings['social_whatsapp'] ?? 'https://wa.me/'.config('shop.whatsapp_number', '8801641857715') }}" target="_blank" rel="noopener noreferrer" class="socialContainer containerThree" aria-label="WhatsApp">
            <i class="fab fa-whatsapp socialIcon"></i>
        </a>
    </div>
</div>

@pushOnce('styles')
<style>
.card-social {
    width: fit-content;
    height: fit-content;
    background-color: rgb(238, 238, 238);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 25px 25px;
    gap: 20px;
    box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.055);
    flex-direction: column;
    border-radius: 16px;
}

.dark .card-social {
    background-color: rgb(30, 30, 30);
}

.socialContainer {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background-color: rgb(44, 44, 44);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    transition-duration: 0.3s;
}

.containerOne:hover {
    background-color: #1877F2;
    transition-duration: 0.3s;
}

.containerTwo:hover {
    background-color: #d62976;
    transition-duration: 0.3s;
}

.containerThree:hover {
    background-color: #128c7e;
    transition-duration: 0.3s;
}

.socialContainer:active {
    transform: scale(0.9);
    transition-duration: 0.3s;
}

.socialIcon {
    color: rgb(255, 255, 255);
    font-size: 17px;
}

.socialContainer:hover .socialIcon {
    animation: slide-in-top 0.3s both;
}

@keyframes slide-in-top {
    0% {
        transform: translateY(-50px);
        opacity: 0;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>
@endPushOnce
