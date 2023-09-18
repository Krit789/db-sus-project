import {getToken} from "#auth";

export default defineEventHandler(async (event) => {
    const jwt = await getToken({event});
    const body = await readBody(event); // Read from POST body
    let url: string;

    if (body.usage === "admin") {
        url = "http://localhost:3000/proxy/api/admin.php"
    } else if (body.usage === "user") {
        url = "http://localhost:3000/proxy/api/user.php"
    } else if (body.usage === "manager") {
        url = "http://localhost:3000/proxy/api/manager.php"
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
    }
    setResponseStatus(event, 418);
    return {
        status: 0,
        message: "Invalid Session Token",
    };
});
