export async function getJson<T>(
  path: string,
  params: Record<string, string> = {},
): Promise<T> {
  const response = await fetch(
    `${import.meta.env.VITE_API_URL}${path}?${new URLSearchParams(params)}`,
  );
  const body: unknown = await response.json().catch(() => null);
  if (!response.ok) {
    throw new Error(errorText(body, response.status));
  }
  return body as T;
}

function errorText(body: unknown, status: number): string {
  return isErrorBody(body)
    ? body.error
    : `A szerver hibával válaszolt (HTTP ${status})`;
}

function isErrorBody(body: unknown): body is { error: string } {
  return (
    typeof body === "object" &&
    body !== null &&
    "error" in body &&
    typeof body.error === "string"
  );
}

export function errorMessage(error: unknown): string {
  return error instanceof Error ? error.message : String(error);
}
