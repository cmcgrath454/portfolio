function analyze(btn) {
  btn.disabled = true;
  fetch('https://fz6lk4txy6.execute-api.us-east-2.amazonaws.com/default', {
    method: 'POST',
    body: document.getElementById("code").value,
  })
    .then((response) => response.json()
      .then(body => processResponse(body, response.status))
    );
}

function processResponse(body, status) {
  const codeFeedback = document.getElementById('code-feedback');
  switch (status) {
    case 422:
      let errors = body.message.match(/line: (\d*)/);
      if (errors) {
        const errorLocation = errors[1];
        codeFeedback.innerHTML = '<span class="text-danger"> Invalid Java code detected at line ' + errorLocation + '</span>';
      } else {
        codeFeedback.innerText = '<span class="text-danger">Invalid Java code detected</span>';
      }
      break;
    case 201:
      document.getElementById('solution').innerText = "";
      const formattedResult = body.result.replace(/\^(\d*)/g, '<sup>$1</sup>').replaceAll('N', 'n');
      document.getElementById('solution').insertAdjacentHTML('beforeend', 'Result: ' + formattedResult);
      let inputCode = document.getElementById('code').value;
      if (body.unsupported.length == 0) {
        codeFeedback.innerHTML = inputCode;
      } else {
        body.unsupported.reverse().forEach((loc) => {
          inputCode = inputCode.slice(0, loc.start) + '<span class="text-danger text-decoration-underline">' + inputCode.slice(loc.start, loc.end + 1) + '</span>' + inputCode.slice(loc.end + 1);
        });
      }
      codeFeedback.innerHTML = inputCode;
      break;
    default:
      codeFeedback.innerText = "Sorry there is an issue with the API right now. Please try again later.";
      break;
  }
  codeFeedback.scrollIntoView();
  document.getElementById('analyze-btn').disabled = false;
}
