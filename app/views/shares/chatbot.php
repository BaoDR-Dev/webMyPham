 <!-- Chatbot Toggler -->
 <button id="chatbot-toggler">
     <span class="material-symbols-rounded">mode_comment</span>
     <span class="material-symbols-rounded">close</span>
 </button>
 <div class="chatbot-popup">
     <!-- Chatbot Header -->
     <div class="chat-header">
         <div class="header-info">
             <img class="chatbot-logo" src="<?= BASE_URL ?>/public/images/Logo.png" />
             <h2 class="logo-text">Trung Tâm Hỗ Trợ Khách Hàng</h2>
         </div>
         <button id="close-chatbot" class="material-symbols-rounded">keyboard_arrow_down</button>
     </div>
     <!-- Chatbot Body -->
     <div class="chat-body">
         <div class="message bot-message">
             <img class="bot-avatar" src="<?= BASE_URL ?>/public/images/CSKH.png" />
             <!-- prettier-ignore -->
             <div class="message-text"> Hello 👋<br />Tôi là nhân viên chăm sóc khách hàng của The Boys Cosmetics.<br />Tôi có thể giúp gì cho bạn hôm nay? </div>
         </div>
     </div>
     <!-- Chatbot Footer -->
     <div class="chat-footer">
         <form action="#" class="chat-form">
             <textarea placeholder="Nhập tin nhắn..." class="message-input" required></textarea>
             <div class="chat-controls">
                 <button type="button" id="emoji-picker" class="material-symbols-outlined">sentiment_satisfied</button>
                 <div class="file-upload-wrapper">
                     <input type="file" id="file-input" hidden />
                     <img src="#" />
                     <button type="button" id="file-upload" class="material-symbols-rounded">attach_file</button>
                     <button type="button" id="file-cancel" class="material-symbols-rounded">close</button>
                 </div>
                 <button type="submit" id="send-message" class="material-symbols-rounded">arrow_upward</button>
             </div>
         </form>
     </div>
 </div>