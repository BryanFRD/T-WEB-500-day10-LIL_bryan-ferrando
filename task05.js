const typeRegex = /^[a-zA-Z\-]{3,10}$/;
const brandRegex = /^[a-zA-Z0-9&\-]{2,20}$/;
const form = document.querySelector('form');
const button = document.querySelector('button[type="submit"]');
const canSubmit = [false, false, false, false];


type.addEventListener('keyup', ({currentTarget}) => handleDynamicErrors(currentTarget, typeHelp, 0));
brand.addEventListener('keyup', ({currentTarget}) => handleDynamicErrors(currentTarget, brandHelp, 1));
price.addEventListener('keyup', ({currentTarget}) => handleDynamicErrors(currentTarget, priceHelp, 2));
stock.addEventListener('keyup', ({currentTarget}) => handleDynamicErrors(currentTarget, stockHelp, 3));

const handleSubmit = (event) => {
  event.preventDefault();
  const formData = new FormData(event.currentTarget);
  const type = formData.get('type');
  const brand = formData.get('brand');
  const price = formData.get('price');
  const stock = formData.get('stock');
  
  if(type.match(typeRegex) && brand.match(brandRegex)) {
    fetch(`task05.php?type=${type}&brand=${brand}&price=${price}&stock=${stock}`)
      .then(response => response.json())
      .then(data => {
        const alert = document.querySelector('.alert');
        alert.classList.remove('d-none');
        alert.classList.add('d-block');
        alert.classList.add(data.success ? 'alert-success' : 'alert-danger');
        alert.classList.remove(!data.success ? 'alert-success' : 'alert-danger');
        alert.innerText = data.success ? data.message : data.error;
      });
  }
}

const handleDynamicErrors = (input, ul, index) => {
  let b = true;
  for(let li of ul.children){
    if(input.value.match(li.dataset.regex)) {
      li.classList.add('text-success');
      li.classList.remove('text-danger');
    } else {
      b = false;
      li.classList.add('text-danger');
      li.classList.remove('text-success');
    }
  }
  canSubmit[index] = b;
  button.disabled = !canSubmit.every(x => x);
}

handleDynamicErrors(type, typeHelp, 0);
handleDynamicErrors(brand, brandHelp, 1);
handleDynamicErrors(price, priceHelp, 2);
handleDynamicErrors(stock, stockHelp, 3);