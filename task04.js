const typeRegex = /^[a-zA-Z\-]{3,10}$/;
const brandRegex = /^[a-zA-Z0-9&\-]{2,20}$/;

const handleSubmit = (event) => {
  event.preventDefault();
  const formData = new FormData(event.currentTarget);
  const type = formData.get('type');
  const brand = formData.get('brand');
  
  if(type.match(typeRegex) && brand.match(brandRegex)) {
    fetch(`task04.php?type=${type}&brand=${brand}`, {
      headers: {
        'Content-Type': 'application/json'
      },
      method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
      console.log(data);
      const alert = document.querySelector('.alert');
      alert.classList.remove('d-none');
      alert.classList.add('d-block');
      alert.classList.add(data.success ? 'alert-success' : 'alert-danger');
      alert.classList.remove(!data.success ? 'alert-success' : 'alert-danger');
      alert.innerText = data.success ? data.name : data.error;
    });
  }
}