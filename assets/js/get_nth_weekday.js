
function getTuesday(month, year, nth) {
    var d = new Date(year, month, 1),
      tuesdays = [];

    let yourDate = new Date();
    const offset = yourDate.getTimezoneOffset();

    d.setDate(d.getDate() + (9 - d.getDay()) % 7);
    while (d.getMonth() === month) {
      tuesdays.push(new Date(d.getTime() - (offset*60*1000)).toISOString().split('T')[0]);
      d.setDate(d.getDate() + 7);
    }

    return tuesdays[nth];
  }

const d = new Date();
document.getElementById("InputTalkDate").value = getTuesday(d.getMonth(), d.getFullYear(), 2);

