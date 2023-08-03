/**
 * Represents a book.
 * @constructor
 * @param {any} rows - The title of the book.
 * @param {any} fields - The author of the book.
 */

import { mysqlconnFn } from "$lib/db/mysql";

export async function load() {
  let mysqlconn = await mysqlconnFn();
  try {
    let results = await mysqlconn
      .query("SELECT * FROM users;")
      .then(function ([rows, fields]) {
        //        console.log(rows);
        return rows;
      });

    return {
      data: results,
    };
  } catch (error) {
    console.error("Got an error!!!");
    console.log(error);
    return error;
  }
}
