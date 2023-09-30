import {getToken} from "#auth";

export default defineEventHandler(async (event) => {
    const jwt = await getToken({event});
    const postBody = await readBody(event); // Read from POST body

    let url: string;
    if (postBody.usage === "admin") {
        url = "/proxy/api/admin.php";
    } else if (postBody.usage === "user") {
        url = "/proxy/api/user.php";
    } else if (postBody.usage === "manager") {
        url = "/proxy/api/manager.php";
    } else {
        setResponseStatus(event, 451);
        return {
            status: 0,
            message: "Invalid Usage",
        };
    }

    if (jwt?.token) {
        const tokenObj: any = {token: jwt?.token};
        const result: any = Object.assign({}, postBody, tokenObj);

        const reservations = await $fetch(url, {
            method: "POST",
            body: result,
        }).catch((error) => error.data);
        return reservations;
    } else {
        setResponseStatus(event, 418);
        return {
            status: 0,
            message: "Invalid Session Token",
        };
    }
});
