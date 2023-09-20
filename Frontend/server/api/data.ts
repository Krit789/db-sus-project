import {getToken} from "#auth";

export default defineEventHandler(async (event) => {
    const jwt = await getToken({event});
    const body = await readBody(event); // Read from POST body
    let url: string;
    // console.log(jwt);
    if (body.usage === "admin") {
        url = "/proxy/api/admin.php"
    } else if (body.usage === "user") {
        url = "/proxy/api/user.php"
    } else if (body.usage === "manager") {
        url = "/proxy/api/manager.php"
    } else {
        setResponseStatus(event, 451);
        return {
            status: 0,
            message: "Invalid Usage",
        }
    }

    if (jwt?.token) {
        const reservations = await $fetch(
            url,
            {
                method: "POST",
                body: {
                    type: body.type,
                    token: jwt.token,
                },
            },
        ).catch((error) => error.data);
        return reservations;
    } else {
        setResponseStatus(event, 418);
        return {
            status: 0,
            message: "Invalid Session Token",
        };
    }

});
