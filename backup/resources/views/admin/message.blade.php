@extends('admin/layouts/master')
@section('container')
<main class="main-content">
    <div class="container-fluid px-4">
    
    <div class="row">
        <div class="col-8">
            <div class="messages-header">
            <h2 class="m-0">Messages</h2>
            </div>
        </div>    
        
        <div class="col-4">
            <div class="massage-search-container">
                <div class="massage-search-icon"><i class="bi bi-search"></i></div>
                <input type="text" class="massage-search-input" placeholder="Search">
            </div>
        </div>
    </div>    
            
    <div class="row">
        <div class="col-4 col-md-4 col-lg-4">
            <div class="message-sidebar">
                <div class="message-sidebar-header">
                    <h4>Messages</h4>
                    <div class="menu-icon">⋮</div>
                </div>
                
                <div class="messages-subheader">
                    Lorem ipsum
                </div>
                
                <ul class="contact-list">
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Albert Flores">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Albert Flores</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                        <div class="online-indicator"></div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Leslie Alexander">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Leslie Alexander</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                        <div class="online-indicator"></div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Devon Lane">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Devon Lane</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                        <div class="online-indicator"></div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Bessie Cooper">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Bessie Cooper</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Esther Howard">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Esther Howard</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Alice Thompson">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Alice Thompson</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Evelyn Reed">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Evelyn Reed</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Margaret Hernandez">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Margaret Hernandez</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Hattie Simmons">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Hattie Simmons</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Olivia Richardson">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Olivia Richardson</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Violet Coleman">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Violet Coleman</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Lucille Hughes">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Lucille Hughes</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                    </li>
                    <li class="contact-item">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Genevieve Sanders">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">Genevieve Sanders</div>
                            <div class="contact-preview">Lorem ipsum dolor sit amet</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div> 
    
        <div class="col-8 col-md-8 col-lg-8">
            <div class="chat-container">
                <div class="chat-header">
                    <div class="chat-contact">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Support Admin">
                        </div>
                        <div class="chat-title">
                            <div class="chat-name">Suporte ADMIN</div>
                            <div class="chat-status">#CU6798H</div>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <div class="action-icon">
                         <i class="bi bi-telephone-fill" style="color: #7b5caf;"></i>
                        </div>
                        <div class="action-icon">
                         <i class="bi bi-camera-video-fill" style="color: #7b5caf;"></i>
                        </div>
                        <div class="action-icon">
                            <i class="bi bi-info-circle-fill" style="color: #7b5caf;"></i>
                        </div>
                    </div>
                </div>
                
                <div class="messages-area">
                     <!-- User Message -->
                    <div class="message user">
                        <div class="avatar">OP</div>
                        <div class="message-content">
                            <div class="message-text">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</div>
                            <div class="message-time">8:00 PM</div>
                        </div>
                    </div>

                    <div class="message bot">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Bot Avatar" class="bot-avatar-img">
                        </div>
                        <div class="message-content">
                            <div class="message-text">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</div>
                            <div class="message-time">8:00 PM</div>
                        </div>
                    </div>


                    <div class="message user">
                        <div class="avatar">OP</div>
                        <div class="message-content">
                            <div class="message-text">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</div>
                            <div class="message-time">8:00 PM</div>
                        </div>
                    </div>

                    <div class="message bot">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Bot Avatar" class="bot-avatar-img">
                        </div>
                        <div class="message-content">
                            <div class="message-text">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</div>
                            <div class="message-time">8:00 PM</div>
                        </div>
                    </div>


                    <div class="message user">
                        <div class="avatar">OP</div>
                        <div class="message-content">
                            <div class="message-text">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</div>
                            <div class="message-time">8:00 PM</div>
                        </div>
                    </div>

                    <div class="message bot">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Bot Avatar" class="bot-avatar-img">
                        </div>
                        <div class="message-content">
                            <div class="message-text">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</div>
                            <div class="message-time">8:00 PM</div>
                        </div>
                    </div>
                    <div class="message user">
                        <div class="avatar">OP</div>
                        <div class="message-content">
                            <div class="message-text">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</div>
                            <div class="message-time">8:00 PM</div>
                        </div>
                    </div>

                    <div class="message bot">
                        <div class="avatar">
                            <img src="{{ asset('public/40.png')}}" alt="Bot Avatar" class="bot-avatar-img">
                        </div>
                        <div class="message-content">
                            <div class="message-text">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</div>
                            <div class="message-time">8:00 PM</div>
                        </div>
                    </div>

                    
                    
                    <div class="message-divider">
                        <span>New message</span>
                    </div>
                    
                    <div class="message user">
                        <div class="avatar">OP</div>
                        <div class="message-content">
                            <div class="message-text">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</div>
                            <div class="message-time">8:00 PM</div>
                        </div>
                    </div>

                    
                </div>
                
                <div class="input-area">
                    <input type="text" class="message-input" placeholder="Enter your message">
                    <button class="attachment-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                        </svg>
                    </button>
                    <button class="emoji-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                            <line x1="9" y1="9" x2="9.01" y2="9"></line>
                            <line x1="15" y1="9" x2="15.01" y2="9"></line>
                        </svg>
                    </button>
                    <button class="send-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    </div>
</main>
@endsection