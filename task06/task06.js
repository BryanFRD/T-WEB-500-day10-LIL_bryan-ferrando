const handleSubmit = (event) => {
  event.preventDefault();
  const {name, message} = Object.fromEntries(new FormData(event.currentTarget));
  
  if(!name || !message)
    return;
    
  messageInput.value = '';
}

const handleMessage = ({username, message}) => {
  const div = document.createElement('div');
  div.classList.add('bg-secondary', 'bg-opacity-25', 'w-auto', 'p-3', 'rounded-3')
  const h3 = document.createElement('h3');
  h3.innerText = username;
  const span = document.createElement('span');
  span.innerText = message;
  
  div.append(h3, span);
  chatBox.appendChild(div);
}

setInterval(() => {handleMessage({username: 'Test', message: 'Message' + Math.floor(Math.random() * 100)})}, 500);

form.addEventListener('submit', handleSubmit);