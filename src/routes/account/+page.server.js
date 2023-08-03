// export async function load() {
//     const user = 1;
//     let result;
//     const res = await fetch('http://localhost:5173/api/user', {
//         method: 'POST',
//         body: JSON.stringify({
//             user: user
//         }),	
//         headers: {
//             'Content-Type': 'application/json'
//         }

//     })
//     const json = await res.json()
//     result = json
//     console.log(result);
//     return {
//         data: result
//     }
// }