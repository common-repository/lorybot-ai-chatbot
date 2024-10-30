document.addEventListener("DOMContentLoaded", function() {


    
    const metaViewport = document.querySelector('meta[name="viewport"]');
    metaViewport.setAttribute('content', 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no');
    


    function initializeChatbot() {
        const rootStyle = document.documentElement.style;
        rootStyle.setProperty('--main-color', chatbot_vars.main_color);
        rootStyle.setProperty('--background-color', chatbot_vars.background_color);
        rootStyle.setProperty('--title-color', chatbot_vars.title_color);
    
        const profileImageUrl = chatbot_vars.plugin_url + 'assets/images/chatbot-profile.svg'; // Construct full URL
        const userprofileImageUrl = chatbot_vars.plugin_url + 'assets/images/user-profile.svg'; // Construct full URL
        const sendIconUrl = chatbot_vars.plugin_url + 'assets/images/send-message.svg'; // Construct full URL

        const chatbotIconUrl1 = chatbot_vars.plugin_url + 'assets/images/lorybot-chat-white.svg'; // Construct full URL
        const chatbotIconUrl2 = chatbot_vars.plugin_url + 'assets/images/lorybot-chat-black.svg'; // Construct full URL

        rootStyle.setProperty('--lorybot-user-profile', `url('${userprofileImageUrl}')`); // Set the full URL wrapped in url(
        rootStyle.setProperty('--lorybot-chat-white', `url('${chatbotIconUrl1}')`); // Set the full URL wrapped in url()
        rootStyle.setProperty('--lorybot-chat-black', `url('${chatbotIconUrl2}')`); // Set the full URL wrapped in url()
        rootStyle.setProperty('--send-icon-url', `url('${sendIconUrl}')`); // Set the full URL wrapped in url()
        rootStyle.setProperty('--profile-image-url', `url('${profileImageUrl}')`); // Set the full URL wrapped in url()
    }

    // Function to set the user_id cookie for 1 day
    function setUserIdCookie() {
        if (!document.cookie.split(';').some((item) => item.trim().startsWith('user_id='))) {
            var d = new Date();
            d.setTime(d.getTime() + (86400 * 1000)); // Set the cookie to expire in 1 day
            var expires = "expires=" + d.toUTCString();
            var userId = generateUserId();
            document.cookie = "user_id=" + userId + ";" + expires + ";path=/";
        }
    }

    function getCookie(name) {
        let value = `; ${document.cookie}`;
        let parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // Function to generate a unique user ID (UUID)
    function generateUserId() {
        // Simple version of UUID generation logic
        // Consider using a more robust method or a library if needed
        return 'uid_' + Math.random().toString(36).slice(2, 11);
    }

    // Set the user_id cookie if not already set
    setUserIdCookie();
    initializeChatbot();
    

    // Function to send a warmup request to the server
    function sendWarmupRequest() {
        const userId = getCookie('user_id');
        if (userId) {
            const customId = chatbot_vars.custom_id; // Ensure chatbot_vars.custom_id is defined
            const serverURL = chatbot_vars.server_url; // Ensure serverURL is defined without a trailing slash if it causes redirect
            const url = serverURL + "warmup"; // Removed the trailing slash here
            const data = { custom_id: customId, user_id: userId }; // Added user_id if it's needed like in generateResponse
    
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'LORYBOT-API-KEY': customId // Adjust according to your API's expected header for authentication
                },
                body: JSON.stringify(data),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Warmup request successful:', data);
                return data; // Make sure to return data or some response
            })
            .catch(error => {
                console.error('Error during fetch:', error);
            });
        }
    }
    


    var chatLoadingStatus = false;
    var isBold = false;
    var isItalic = false;
    var messageDiv;
    var autoScroll = true;

    const $testMessage = jQuery("#sseMessages");
    const originalMessage = jQuery("#original_message");
    var urls = {};

    jQuery(".chatbot-toggler").on("touchstart click", () => {
        sendWarmupRequest();
        jQuery("#chatbot-container").toggleClass("active");
    });

    jQuery(".chatbot-close-btn").on("touchstart click", () => {
        jQuery("#chatbot-container").removeClass("active");
    });
  
  
    document.getElementById("chatbot-message-form-button").addEventListener("click", function (e) {
        e.preventDefault();
        sendMessage();
    });

    document.getElementById("message").addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
    // Get the message from the input element
    const userMessage = document.getElementById("message").value;

    // Check if the chat is not currently loading and the message is not empty
    if (!chatLoadingStatus && userMessage) {
        // Retrieve user ID from the cookie
        const userId = getCookie('user_id');

        // Construct the chat message HTML using template literals for better readability
        const newChat = `
            <div class="user-message">
                <ul>
                    <li>
                        <span>${userMessage}</span>
                    </li>
                </ul>
                <div class="profile"></div>
            </div>`;

        // Append the new chat message to the messages container
        jQuery(".chatbot-chat-screen .messages").append(newChat);

        // Trigger auto-scrolling
        autoScroll = true;

        // Generate a response based on the user's message and user ID
        generateResponse(userMessage, userId);

        // Clear the input element after sending the message
        document.getElementById("message").value = "";

        // Scroll the chat box to the latest message
        scrollChatBox();
    }
}

        
    
    custom_id = chatbot_vars.custom_id;
    server_url = chatbot_vars.server_url;
   
    function generateResponse(userMessage, userId) {
        const customId = chatbot_vars.custom_id;
        const encodedMessage = encodeURIComponent(userMessage);
        const serverURL = chatbot_vars.server_url;
        // Construct the URL with query parameters, including the API key as necessary
        const url = `${serverURL}chat?custom_id=${customId}&message=${encodedMessage}&user_id=${userId}`;

        // Create and open a new EventSource connection
        const eventSource = new EventSource(url);

        var newChat =
        '<div class="chatbot-message"> <div class="profile"></div><ul><li><span><div class="chatbot-loader"><div></div></div></span></li></ul> </div> ';
        jQuery(".chatbot-chat-screen .messages").append(newChat);
        chatLoadingStatus = true;
        urls = {};
        $testMessage.html("");

        eventSource.onmessage = function (event) {
            try {
            // Attempt to parse the event data as JSON
            const data = JSON.parse(event.data);

            // If parsing is successful, handle the JSON data (assuming 'message' field exists)
            processMessage(event, "incoming", true);
            } catch (error) {
            // If the data isn't valid JSON, assume it's plain text and handle directly
            processMessage(event, "incoming", true);
            }
        // Consideration for closing the eventSource should be here, based on the data or a specific condition
        };

        eventSource.onerror = function (error) {
            // Handle any errors
            printOutputReal();
            printOutputReal();
            printOutputReal();
            chatLoadingStatus = false;
            // console.error("SSE error:", error);
            eventSource.close();
        };
    }
  
    function processMessage(event, type, shouldScroll) {
        var message = event.data;
        originalMessage.append(message);

        message = message.replace(/</g, "&lt;"); // replace < sign with lt because it fades away in DOM
        message = message.replace(/&lt;br\s*\/?>/gi, " nEwBoX "); // check for <br> and replace with newbox
        messageDiv = jQuery(".chatbot-chat-screen .messages .chatbot-message")
        .last()
        .find("ul li")
        .last()
        .find("span")
        .last();

        $testMessage.append(message); // first message comes here
        $testMessage.html(
            $testMessage
            .html()
            .replace(/(?<!nEwBoX\s*)\bnEwBoX\b(?!.*\bnEwBoX\b)/g, "NeWlInE")
        ); // check for single appearance of <br> and replace it with newline
        checkForURLs();

        if (($testMessage.text().match(/ /g) || [].length).length > 3) {
            printOutputReal(); // message gets printed on actual place
        }
    }
  
    function checkForURLs() {
        // find urls in test message and mark there starting and ending locations
        const text = $testMessage.text();
        const regex =
        /(https?:\/\/(?!.*eNdOfUrLeNdOfUrL)\S+)|(www\.(?!.*eNdOfUrLeNdOfUrL)\S+)/gi;

        let match;
        while ((match = regex.exec(text))) {
            const url = match[0];
            if (!(url in urls) && isValidURL(url)) {
                urls[url] = true;
                insertLineBreakAroundURL(url);
            }
        }
    }
  
    // print message in actual position
    function printOutputReal() {
        jQuery(".chatbot-chat-screen .messages .chatbot-message .chatbot-loader").remove(); // remove loader

        var sourceText = $testMessage.text();
        var index = sourceText.indexOf(" ");

        // these two if replaces word with sourcetext if there is only one word left in test message
        if (index == 0) {
            var index = sourceText.indexOf(" ", 1);
        }
        var word = sourceText.slice(0, index);

        if (index <= 0) {
            var word = sourceText;
        }

        // print url based on marekd positions
        if (word.includes("sTaRtOfUrL") && word.includes("eNdOfUrL")) {
            // restyle the url now
            var startIndex = word.indexOf("sTaRtOfUrL") + "sTaRtOfUrL".length;
            var endIndex = word.indexOf("eNdOfUrL");
            word = word.slice(startIndex, endIndex);

            var punctuation = [".", ")", "]", "'", '"', "!"];

            while (punctuation.includes(word.slice(-1))) {
                word = word.slice(0, -1);
            }

            jQuery(".chatbot-chat-screen .messages .chatbot-message")
            .last()
            .find("ul")
            .last()
            .find("li")
            .last()
            .append("<a class='link'></a>");

            messageDiv = jQuery(".chatbot-chat-screen .messages .chatbot-message")
            .last()
            .find("ul li")
            .last()
            .find("a")
            .last();

            // if url has ](  in between then we split into two, frst half is the text and second half is actual link
            if (word.includes("](")) {
                var splitText = word.split("](");
                var aa = splitText[0];
                var bb = splitText[1];
                messageDiv.append(aa);
                messageDiv.attr("href", bb);
            } else {
                messageDiv.append(word);
                messageDiv.attr("href", word);
            }
            jQuery(".chatbot-chat-screen .messages .chatbot-message")
            .last()
            .find("ul")
            .last()
            .find("li")
            .last()
            .append("<span></span>");

            messageDiv = jQuery(".chatbot-chat-screen .messages .chatbot-message")
            .last()
            .find("ul li")
            .last()
            .find("span")
            .last();
        } 
        else {
            // check for bold
            if (word.includes("**") && !isBold) {
                word = word.replace(/\*\*/, "");
                isBold = true;
            } else if (word.includes("**") && isBold) {
                word = word.replace(/\*\*/g, "");
                word = "<b class='bold'>" + word + "</b>";
                isBold = false;
            }
            if (isBold) {
                if (word.includes("**")) {
                    word = word.replace(/\*\*/g, "");
                    isBold = false;
                }
                word = "<b class='bold'>" + word + "</b>";
            }

            // check for italic
            if (word.includes("*") && !isItalic) {
                word = word.replace(/\*/, "");
                isItalic = true;
            } else if (word.includes("*") && isItalic) {
                word = word.replace(/\*/g, "");
                word = "<b class='italic'>" + word + "</b>";
                isItalic = false;
            }
            if (isItalic) {
                if (word.includes("*")) {
                    word = word.replace(/\*/g, "");
                    isItalic = false;
                }
                word = "<b class='italic'>" + word + "</b>";
            }

            // check for linebreaks for new box
            if (word.includes("nEwBoX")) {
                if (messageDiv.html()) {
                    if (messageDiv.html().trim() !== "") {
                        word = word + "<br />";
                    }
                }

                word = word.replace(/nEwBoX/g, "");
            }
            
            // check for linbreaks for single line
            if (word.includes("NeWlInE")) {
                if (messageDiv.html()) {
                    if (messageDiv.html().trim() !== "") {
                        word = word.replace(/NeWlInE/g, "<br />");
                    } else {
                        word = word.replace(/NeWlInE/g, "");
                    }
                }
            }
            messageDiv.append(word);
        }

        scrollChatBox();

        if (index >= 0) {
            $testMessage.html(sourceText.slice(index));
        } else {
            $testMessage.html("");
        }
    }
  
    function insertLineBreakAroundURL(url) {
        var html = $testMessage.html();

        var startIndex = html.indexOf(url);
        var endIndex = html.indexOf(url) + url.length;
        url = url.replace(/eNdOfUrL/g, "");

        var newText =
        html.slice(0, startIndex) +
        "sTaRtOfUrL" +
        url +
        "eNdOfUrL" +
        html.slice(endIndex);
        newText = newText.replace(/(sTaRtOfUrL)+/g, "sTaRtOfUrL");
        $testMessage.html(newText);
    }
  
    function isValidURL(url) {
        // Define valid characters for URL
        const validCharacters = /^[a-zA-Z0-9\-._~:/?#\[\]@!$&'()*+,;=%]*$/;
        // Check if the URL ends with a valid character
        return validCharacters.test(url[url.length - 1]);
    }
  
    // scroll to the bottom
    function scrollChatBox() {
        if (autoScroll) {
            var content = jQuery(".chatbot-chat-screen .messages");
            content.scrollTop(content.prop("scrollHeight"));
        }
    }
  
    jQuery(".chatbot .chatbot-content .messages").on("wheel", function (event) {
        handleScroll(jQuery(this));
    });

    jQuery(".chatbot .chatbot-content .messages").on("touchmove", function (event) {
        handleScroll(jQuery(this));
    });
  
    function handleScroll(element) {
        if (element.scrollTop() < element.prop("scrollHeight") - element.height()) {
            autoScroll = false;
        }
        if (element.scrollTop() > element.prop("scrollHeight") - element.height() - 50) {
            autoScroll = true;
        }
    }
});