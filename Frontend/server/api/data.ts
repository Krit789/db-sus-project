import {getToken} from "#auth";

export default defineEventHandler(async (event) => {
    const jwt = await getToken({ event });
    const body = await readBody(event); // Read from POST body
    if (jwt?.token) {
        const reservations = await $fetch(
            "http://localhost:3000/proxy/api/control.php",
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
