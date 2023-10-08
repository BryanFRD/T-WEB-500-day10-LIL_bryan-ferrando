const refreshChatText = refreshChat.querySelector('span');
let autoScroll = true;
let fetchingMessages = false;
const timeout = 100;
const refreshRate = 4000;
let deltaTime = 4000;

const handleSubmit = (event) => {
  event.preventDefault();
  const {name, message} = Object.fromEntries(new FormData(event.currentTarget));
  
  if(!name || !message)
    return;
  
  messageInput.value = '';
  
  fetch('./task06.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({username: name, message})
  })
  .then(response => {
    const json = response.json().catch((err) => {
      console.log(err);
      ({success: false, error: 'Something went wrong'})
    });
    return json;
  })
  .then();
}

const handleRefreshButton = () => {
  refreshChat.disabled = fetchingMessages;
  let seconds = Math.max(0, Math.min(Math.floor((refreshRate - deltaTime) / 1000) + 1, refreshRate))
  refreshChat.innerText = `Refreshing in ${seconds}`;
}

const fetchMessages = () => {
  fetchingMessages = true;
  
  fetch('./task06.php')
    .then(response => {
      const json = response.json().catch((err) => {
        console.log(err);
        ({success: false, error: 'Something went wrong'})
      });
      return json;
    })
    .then(data => {
      if(!data.success)
        return;
      
      chatBox.innerHTML = '<div class="mt-auto"></div>';
      data.messages?.forEach(handleMessage);
    })
    .finally(() => {
      deltaTime = 0;
      fetchingMessages = false;
    })
}

const handleMessage = ({username, message}) => {
  const div = document.createElement('div');
  div.classList.add('bg-secondary', 'bg-opacity-25', 'w-min', 'p-3', 'rounded-3');
  const h3 = document.createElement('h3');
  h3.innerText = username;
  const span = document.createElement('span');
  span.innerText = message;
  
  div.append(h3, span);
  chatBox.appendChild(div);
  
  if(autoScroll)
    chatBox.scrollTop = chatBox.scrollHeight;
}

const handleMessagesInterval = () => {
  deltaTime += timeout;
  handleRefreshButton();
  if(fetchingMessages || deltaTime < refreshRate)
    return;
  
  fetchMessages();
}

setInterval(handleMessagesInterval, timeout);

const handleScroll = () => {
  autoScroll = chatBox.scrollTop === (chatBox.scrollHeight - chatBox.clientHeight);
}

const chatObserver = new ResizeObserver(() => {
  if(autoScroll)
    chatBox.scrollTop = chatBox.scrollHeight;
});

chatObserver.observe(chatBox);
chatBox.addEventListener('scroll', handleScroll);
form.addEventListener('submit', handleSubmit);
refreshChat.addEventListener('click', fetchMessages);