const typeRegex = /^[a-zA-Z\-]{3,10}$/;
const brandRegex = /^[a-zA-Z0-9&\-]{2,20}$/;
const form = document.querySelector('form');
const button = document.querySelector('button[type="submit"]');
const canSubmit = [false, false, false, false];
const alert = document.querySelector('.alert');
const table = document.querySelector('table');

const inputs = [[type, typeHelp], [brand, brandHelp]]

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
    const tbody = table.querySelector('tbody');
    tbody.innerHTML = '';
    fetch(`task05.php?type=${type}&brand=${brand}&price=${price}&number=${stock}`)
      .then(response => response.json())
      .then(data => {
        if(!data.success){
          alert.innerText = data.error;
          alert.classList.remove('d-none');
          table.classList.add('d-none');
        } else {
          alert.classList.add('d-none');
          console.log(data.products)
          
          for(let product of data.products){
            const tr = document.createElement('tr');
            for(let th of table.querySelectorAll('th')){
              const td = document.createElement('td');
              td.innerText = product[th.dataset.field];
              tr.appendChild(td);
            }
            tbody.appendChild(tr);
          }
          
          table.classList.remove('d-none');
        }
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