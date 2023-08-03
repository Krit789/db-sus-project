import { json } from "@sveltejs/kit";
import { mysqlconnFn } from "$lib/db/mysql";

export async function POST({ request }) {
  const { user, password } = await request.json();
  let mysqlconn = await mysqlconnFn();
  let results = await mysqlconn
    .query(`SELECT token FROM users WHERE email='${user}' AND password_hash = '${password}'`)
    .then(function ([rows, fields]) {
      //     console.log("Got this far!!");
      //     console.log(rows);
      return rows;
    });

  return json(results);
}